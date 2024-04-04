<?php

declare(strict_types=1);

use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();
    // add NullLogger due to http-exceptions not being caught, see https://github.com/symfony/symfony/issues/28023
    $container->services()
        ->set('logger', NullLogger::class);
};
