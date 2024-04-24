<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Unit\DependencyInjection\Compiler;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Configuration\YamlParser;
use Qossmic\TwigDocBundle\DependencyInjection\Compiler\TwigDocCollectDocsPass;
use Qossmic\TwigDocBundle\DependencyInjection\TwigDocExtension;
use Qossmic\TwigDocBundle\Exception\InvalidConfigException;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

#[CoversClass(TwigDocCollectDocsPass::class)]
#[UsesClass(YamlParser::class)]
class TwigDocCollectDocsPassTest extends TestCase
{
    public function testProcessNotExecutedWhenExtensionIsMissing(): void
    {
        $container = new ContainerBuilder();

        $pass = new TwigDocCollectDocsPass(new YamlParser());
        $pass->process($container);

        static::assertTrue(true);
    }

    public function testProcess(): void
    {
        $container = $this->getContainer(directories: [__DIR__.'/../../../TestApp/templates/snippets', 'notADirectory']);

        $pass = new TwigDocCollectDocsPass(new YamlParser());
        $pass->process($container);

        $service = $container->getDefinition('twig_doc.service.component');

        static::assertCount(2, $service->getArgument('$componentsConfig'));
    }

    public function testProcessNotEnrichingPathsForMissingTemplate(): void
    {
        $container = $this->getContainer(componentsConfig: [
            [
                'name' => 'invalidComponent',
            ],
        ]);

        $pass = new TwigDocCollectDocsPass(new YamlParser());
        $pass->process($container);

        $definition = $container->getDefinition('twig_doc.service.component');

        static::assertArrayNotHasKey('path', $definition->getArgument('$componentsConfig')[0]);
        static::assertArrayNotHasKey('renderPath', $definition->getArgument('$componentsConfig')[0]);
    }

    public function testProcessNotEnrichingPathsForAmbiguousTemplate(): void
    {
        $container = $this->getContainer(componentsConfig: [
            [
                'name' => 'SomeComponent',
            ],
            [
                'name' => 'SomeComponent',
            ],
        ], directories: [
            '%twig.default_path%/invalid_for_test',
        ]);

        $pass = new TwigDocCollectDocsPass(new YamlParser());
        $pass->process($container);

        $definition = $container->getDefinition('twig_doc.service.component');

        static::assertArrayNotHasKey('path', $definition->getArgument('$componentsConfig')[0]);
        static::assertArrayNotHasKey('renderPath', $definition->getArgument('$componentsConfig')[0]);
        static::assertArrayNotHasKey('path', $definition->getArgument('$componentsConfig')[1]);
        static::assertArrayNotHasKey('renderPath', $definition->getArgument('$componentsConfig')[1]);
    }

    public function testProcessThrowsExceptionForInvalidConfiguration(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(sprintf('Component "%s" is configured twice, please configure either directly in the template or the general bundle configuration', 'Button'));

        $container = $this->getContainer([
            [
                'name' => 'Button',
            ],
        ]);

        $pass = new TwigDocCollectDocsPass(new YamlParser());
        $pass->process($container);
    }

    private function getContainer(array $componentsConfig = [], array $directories = []): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new TwigDocExtension());
        $container->setParameter('kernel.project_dir', __DIR__.'/../../../TestApp');
        $container->setParameter('twig.default_path', __DIR__.'/../../../TestApp/templates');
        $container->setParameter('twig_doc.config', [
            'doc_identifier' => 'TWIG_DOC',
            'directories' => $directories,
            'categories' => ['category'],
        ]);
        $definition = new Definition(ComponentService::class, [
            '$componentsConfig' => $componentsConfig,
        ]);

        $container->setDefinition('twig_doc.service.component', $definition);

        return $container;
    }
}
