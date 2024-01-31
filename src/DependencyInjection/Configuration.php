<?php

namespace Qossmic\TwigDocBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('twig_doc');

        $treeBuilder->getRootNode()
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
        ->variableNode('parameters')
            ->validate()
                ->ifTrue(fn ($v) => is_string($v) === false && is_array($v) === false)
                ->thenInvalid('parameters must be either a scalar or an array')
            ->end()
        ->end()
        ->arrayNode('variations')
        ->arrayPrototype()
        ->scalarPrototype()->end()
        ->end()
        ->end()
        ->end()
        ->end()
        ->end();

        return $treeBuilder;
    }
}