<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Controller\TwigDocController;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Qossmic\TwigDocBundle\Twig\Component\LiveComponent;
use Qossmic\TwigDocBundle\Twig\TwigDocExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('twig_doc.controller.documentation', TwigDocController::class)
            ->public()
            ->autoconfigure()
            ->autowire()
            ->arg('$profiler', service('profiler')->nullOnInvalid())
        ->set('twig_doc.service.category', CategoryService::class)
            ->alias(CategoryService::class, 'twig_doc.service.category')

        ->set('twig_doc.service.component_factory', ComponentItemFactory::class)
            ->public()
            ->autoconfigure()
            ->autowire()
            ->alias(ComponentItemFactory::class, 'twig_doc.service.component_factory')

        ->set('twig_doc.service.component', ComponentService::class)
            ->public()
            ->autoconfigure()
            ->autowire()
            ->alias(ComponentService::class, 'twig_doc.service.component')

        ->set('twig_doc.twig.extension', TwigDocExtension::class)
            ->autoconfigure()
            ->autowire()
            ->tag('twig.extension')
            ->alias(TwigDocExtension::class, 'twig_doc.twig.extension')
        ->set('twig_doc.twig.ux.live.component', LiveComponent::class)
            ->autoconfigure()
            ->autowire();
};
