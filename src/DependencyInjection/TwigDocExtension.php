<?php

namespace Qossmic\TwigDocBundle\DependencyInjection;

use Qossmic\TwigDocBundle\Twig\Component\LiveComponent;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

class TwigDocExtension extends Extension implements PrependExtensionInterface
{

    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('documentation.php');

        $definition = $container->getDefinition('twig_doc.service.component');
        $definition->setArgument('$componentsConfig', $config['components']);

        $definition = $container->getDefinition('twig_doc.service.category');
        $definition->setArgument('$categoriesConfig', $config['categories']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $config = [
            "defaults" => [
                'Qossmic\TwigDocBundle\Twig\Component\\' => '@TwigDoc/component'
            ]
        ];

        $container->prependExtensionConfig('twig_component', $config);
    }
}
