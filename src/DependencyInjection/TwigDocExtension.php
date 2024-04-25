<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\DependencyInjection;

use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

class TwigDocExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('twig_doc.config', $config);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('documentation.php');

        $definition = $container->getDefinition('twig_doc.service.component');
        $definition->setArgument('$componentsConfig', $config['components']);
        $definition->setArgument('$breakpointConfig', $config['components']);
        $definition->setArgument('$configReadTime', time());

        $categories = array_merge([['name' => ComponentCategory::DEFAULT_CATEGORY]], $config['categories']);

        $definition = $container->getDefinition('twig_doc.service.category');
        $definition->setArgument('$categoriesConfig', $categories);

        $definition = $container->getDefinition('twig_doc.service.faker');
        $definition->setArgument('$generators', tagged_iterator('twig_doc.data_generator'));

        $definition = $container->getDefinition('twig_doc.service.component_factory');
        $definition->setArgument('$useFakeParams', $config['use_fake_parameter']);
    }
}
