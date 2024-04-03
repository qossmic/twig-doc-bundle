<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\DependencyInjection\Compiler;

use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Configuration\ParserInterface;
use Qossmic\TwigDocBundle\Exception\InvalidConfigException;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

readonly class TwigDocCollectDocsPass implements CompilerPassInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('twig_doc')) {
            return;
        }

        $config = $container->getParameter('twig_doc.config');
        $directories = $this->resolveDirectories($container, $config['directories']);
        $container->getParameterBag()->remove('twig_doc.config');

        if (empty($directories)) {
            return;
        }

        $definition = $container->getDefinition('twig_doc.service.component');
        $componentConfig = $this->enrichComponentsConfig($container, $directories, $definition->getArgument('$componentsConfig'));
        $projectDir = $container->getParameter('kernel.project_dir');
        $templateDir = $container->getParameter('twig.default_path');

        foreach ($directories as $directory) {
            // add resource to container to rebuild container on changes in templates
            $container->addResource(new DirectoryResource($directory));
        }

        $finder = new Finder();
        foreach ($finder->in($directories)->files()->filter(fn (SplFileInfo $file) => $file->getExtension() === 'twig') as $file) {
            $doc = $this->parseDoc($file, $config['doc_identifier']);

            if ($doc === null) {
                continue;
            }

            $filename = $file->getFilename();
            $componentName = substr($filename, 0, strpos($filename, '.'));

            if (array_filter($componentConfig, static fn (array $data) => $data['name'] === $componentName)) {
                throw new InvalidConfigException(sprintf('Component "%s" is configured twice, please configure either directly in the template or the general bundle configuration', $componentName));
            }

            $itemConfig = [
                'name' => $componentName,
                'path' => str_replace($projectDir.'/', '', $file->getRealPath()),
                'renderPath' => str_replace($templateDir.'/', '', $file->getRealPath()),
                'category' => ComponentCategory::DEFAULT_CATEGORY,
            ];
            $componentConfig[] = array_merge($itemConfig, $doc);
        }

        $definition->replaceArgument('$componentsConfig', $componentConfig);
    }

    private function parseDoc(SplFileInfo $file, string $docIdentifier): ?array
    {
        $content = $file->getContents();
        $pattern = sprintf("/\{#%s\s(.*)%s#}/s", $docIdentifier, $docIdentifier);

        preg_match($pattern, $content, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        return $this->parser->parse($matches[1]);
    }

    private function enrichComponentsConfig(ContainerBuilder $container, array $directories, array $components): array
    {
        foreach ($components as &$component) {
            if (!isset($component['path']) && $templatePath = $this->getTemplatePath($component['name'], $directories)) {
                $component['path'] = str_replace($container->getParameter('kernel.project_dir').'/', '', $templatePath);
            }
            if (!isset($component['renderPath']) && isset($component['path'])) {
                $component['renderPath'] = str_replace($container->getParameter('twig.default_path').'/', '', $component['path']);
            }
        }

        return $components;
    }

    private function resolveDirectories(ContainerBuilder $container, array $directories): array
    {
        $directories[] = $container->getParameter('twig.default_path').'/components';
        $directories = array_map(static fn (string $dir) => $container->getParameterBag()->resolveValue($dir), $directories);

        foreach ($directories as $idx => $dir) {
            if (!is_dir($dir)) {
                unset($directories[$idx]);
            }
        }

        return array_unique($directories);
    }

    private function getTemplatePath(string $name, array $directories): ?string
    {
        $template = sprintf('%s.html.twig', $name);
        $finder = new Finder();
        $files = $finder->in($directories)->files()->filter(fn (SplFileInfo $file) => $file->getFilename() === $template);

        if ($files->count() > 1) {
            return null;
        }

        if (!$files->hasResults()) {
            return null;
        }

        $files->getIterator()->rewind();

        return $files->getIterator()->current()?->__toString();
    }
}
