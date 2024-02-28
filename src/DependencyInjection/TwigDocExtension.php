<?php

namespace Qossmic\TwigDocBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class TwigDocExtension extends Extension
{

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('twig_doc.config', $config);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('documentation.php');

        $definition = $container->getDefinition('twig_doc.service.component');
        $definition->setArgument('$componentsConfig', $config['components']);

        $definition = $container->getDefinition('twig_doc.service.category');
        $definition->setArgument('$categoriesConfig', $config['categories']);
    }
}
