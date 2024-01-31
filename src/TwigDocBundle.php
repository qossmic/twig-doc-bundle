<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TwigDocBundle extends Bundle
{
    /*public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/../config/documentation.php');

        $definition = $container->services()->get('twig_doc.service.component');
        $definition->arg('$componentsConfig', $config['components']);

        $definition = $container->services()->get('twig_doc.service.category');
        $definition->arg('$categoriesConfig', $config['categories']);
    }*/

    public function getPath(): string
    {
        return __DIR__.'/..';
    }
}
