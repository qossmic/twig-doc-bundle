<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Controller\TwigDocController;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Qossmic\TwigDocBundle\Twig\TwigDocExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('twig_doc.controller.documentation', TwigDocController::class)
            ->public()
            ->args([
                service('twig'),
                service('twig_doc.service.component')
            ])
        ->set('twig_doc.service.category', CategoryService::class)

        ->set('twig_doc.service.component_factory', ComponentItemFactory::class)
            ->public()
            ->args([service('validator'), service('twig_doc.service.category')])

        ->set('twig_doc.service.component', ComponentService::class)
            ->public()
            ->args([service('twig_doc.service.component_factory'), service('twig_doc.service.category')])

        ->set('twig_doc.twig.extension', TwigDocExtension::class)
            ->args([
                service('ux.twig_component.component_renderer')->nullOnInvalid(),
                service('twig_doc.service.component'),
                service('twig_doc.service.category'),
                service('twig')]
            )
            ->tag('twig.extension');
};
