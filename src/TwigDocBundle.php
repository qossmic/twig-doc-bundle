<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class TwigDocBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('categories')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->end()
                            ->arrayNode('sub_categories')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('components')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('description')->defaultNull()->end()
                            ->scalarNode('category')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('sub_category')->defaultNull()->end()
                            ->arrayNode('tags')
                                ->scalarPrototype()->end()
                            ->end()
                        ->arrayNode('parameters')
                            ->scalarPrototype()->end()
                        ->end()
                        ->arrayNode('variations')
                            ->arrayPrototype()
                            ->scalarPrototype()->end()
                            ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/../config/documentation.php');

        $definition = $container->services()->get('twig_doc.service.component');
        $definition->arg('$componentsConfig', $config['components']);

        $definition = $container->services()->get('twig_doc.service.category');
        $definition->arg('$categoriesConfig', $config['categories']);
    }
}
