<?php

namespace Qossmic\TwigDocBundle\DependencyInjection\Compiler;

use Qossmic\TwigDocBundle\Configuration\ParserInterface;
use Qossmic\TwigDocBundle\Exception\InvalidConfigException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class TwigDocPass implements CompilerPassInterface
{
    public function __construct(private readonly ParserInterface $parser)
    {
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasExtension('twig_doc')) {
            return;
        }
        $config = $container->getParameter('twig_doc.config');
        $definition = $container->getDefinition('twig_doc.service.component');
        $componentConfig = $definition->getArgument('$componentsConfig');
        $projectDir = $container->getParameter('kernel.project_dir');

        $twigPath = $container->getParameter('twig.default_path') . '/components';

        $paths = [$twigPath];

        $finder = new Finder();
        foreach ($finder->in($paths)->files() as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $doc = $this->parseDoc($file, $config['doc_identifier']);

            if ($doc === null) {
                continue;
            }
            $filename = $file->getFilename();
            $componentName = substr($filename, 0, strpos($filename, '.'));

            if (array_filter($componentConfig, fn(array $data) => $data['name'] === $componentName)) {
                throw new InvalidConfigException(
                    sprintf('component %s is configured twice, please configure either directly in the template or the general bundle configuration', $componentName));
            }
            $itemConfig = [
                'name' => $componentName,
                'path' => str_replace($projectDir.'/', '', $file->getRealPath()),
            ];
            $componentConfig[] = array_merge($itemConfig, $doc);
        }

        $definition->replaceArgument('$componentsConfig', $componentConfig);

        $container->getParameterBag()->remove('twig_doc.config');
    }

    private function parseDoc(SplFileInfo $file, string $docIdentifier): null|array
    {
        $content = $file->getContents();

        $pattern = sprintf("/\{#%s\s(.*)%s#}/s", $docIdentifier, $docIdentifier);

        preg_match($pattern, $content, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        return $this->parser->parse($matches[1]);
    }
}
