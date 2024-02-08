<?php

namespace Qossmic\TwigDocBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class TwigDocExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('documentation.php');

        $definition = $container->getDefinition('twig_doc.service.component');
        $definition->setArgument('$componentsConfig', $config['components']);
        $definition->setArgument('$breakpointConfig', $config['breakpoints']);

        $definition = $container->getDefinition('twig_doc.service.category');
        $definition->setArgument('$categoriesConfig', $config['categories']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        # register bundle namespace for twig-ux-components
        $container->prependExtensionConfig('twig_component', [
            "defaults" => [
                'Qossmic\TwigDocBundle\Twig\Component\\' => '@TwigDoc/component'
            ]
        ]);

        # asset mapper config for js code
        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__.'/../../assets/dist' => '@qossmic/twig-doc-bundle',
                ],
            ],
        ]);
    }
}
