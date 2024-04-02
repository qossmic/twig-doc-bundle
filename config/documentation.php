<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qossmic\TwigDocBundle\Cache\ComponentsWarmer;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Component\Data\Faker;
use Qossmic\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\NullGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use Qossmic\TwigDocBundle\Controller\TwigDocController;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Qossmic\TwigDocBundle\Twig\TwigDocExtension;

return static function (ContainerConfigurator $container) {
    $container->services()->set('twig_doc.controller.documentation', TwigDocController::class)
        ->public()
        ->arg('$twig', service('twig'))
        ->arg('$componentService', service('twig_doc.service.component'))
        ->arg('$profiler', service('profiler')->nullOnInvalid())
        ->set('twig_doc.service.category', CategoryService::class)
        ->alias(CategoryService::class, 'twig_doc.service.category')
        ->set('twig_doc.service.component_factory', ComponentItemFactory::class)
        ->public()
        ->arg('$validator', service('validator'))
        ->arg('$categoryService', service('twig_doc.service.category'))
        ->arg('$faker', service('twig_doc.service.faker'))
        ->alias(ComponentItemFactory::class, 'twig_doc.service.component_factory')
        ->set('twig_doc.service.component', ComponentService::class)
        ->public()
        ->arg('$itemFactory', service('twig_doc.service.component_factory'))
        ->arg('$cache', service('cache.app'))
        ->alias(ComponentService::class, 'twig_doc.service.component')
        ->set('twig_doc.twig.extension', TwigDocExtension::class)
        ->public()
        ->arg('$componentRenderer', service('ux.twig_component.component_renderer')->nullOnInvalid())
        ->arg('$componentService', service('twig_doc.service.component'))
        ->arg('$categoryService', service('twig_doc.service.category'))
        ->arg('$twig', service('twig'))
        ->tag('twig.extension')
        ->alias(TwigDocExtension::class, 'twig_doc.twig.extension')
        ->set('twig_doc.cache_warmer', ComponentsWarmer::class)
        ->arg('$container', service('service_container'))
        ->tag('kernel.cache_warmer')
        ->alias(ComponentsWarmer::class, 'twig_doc.cache_warmer')

        ->set('twig_doc.service.faker', Faker::class)
        ->public()
        ->alias(Faker::class, 'twig_doc.service.faker')

        ->set('twig_doc.data_generator.scalar', ScalarGenerator::class)
        ->public()
        ->tag('twig_doc.data_generator', ['priority' => -5])
        ->set('twig_doc.data_generator.fixture', FixtureGenerator::class)
        ->public()
        ->tag('twig_doc.data_generator', ['priority' => -10])

        // use null-generator as last one to ensure all other are attempted first
        ->set('twig_doc.data_generator.null', NullGenerator::class)
        ->public()
        ->tag('twig_doc.data_generator', ['priority' => -100]);
};
