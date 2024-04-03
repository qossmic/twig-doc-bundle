<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\DependencyInjection\Configuration;
use Qossmic\TwigDocBundle\DependencyInjection\TwigDocExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(TwigDocExtension::class)]
#[UsesClass(Configuration::class)]
class TwigDocExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $extension = new TwigDocExtension();

        $configs = [
            [
                'doc_identifier' => 'TWIG_DOC',
                'directories' => [
                    'some-directory',
                ],
                'categories' => [
                    ['name' => 'category'],
                ],
            ],
        ];

        $extension->load($configs, $container);

        $componentServiceDefinition = $container->getDefinition('twig_doc.service.component');
        $categoryServiceDefinition = $container->getDefinition('twig_doc.service.category');

        static::assertEquals([], $componentServiceDefinition->getArgument('$componentsConfig'));
        static::assertEqualsCanonicalizing([
            ['name' => ComponentCategory::DEFAULT_CATEGORY],
            ['name' => 'category', 'sub_categories' => []],
        ], $categoryServiceDefinition->getArgument('$categoriesConfig'));
    }
}
