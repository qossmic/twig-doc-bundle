<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\TestApp;

use Qossmic\TwigDocBundle\TwigDocBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends SymfonyKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment = 'test', bool $debug = false, private readonly array $extraConfigs = [])
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new TwigDocBundle(),
        ];
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getConfigDir().'/routing/documentation.xml');
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        // test configs
        $loader->load(__DIR__.'/config/services.php');
        $loader->load(__DIR__.'/config/packages/*.php', 'glob');
        $loader->load(__DIR__.'/config/packages/*.yaml', 'glob');

        foreach ($this->extraConfigs as $name => $config) {
            $container->prependExtensionConfig($name, $config);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/qossmic-twig-doc-bundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/qossmic-twig-doc-bundle/log';
    }

    public function getProjectDir(): string
    {
        return __DIR__.'/../..';
    }
}
