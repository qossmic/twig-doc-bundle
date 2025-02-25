<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Tests\TestApp\Test;

use OpenSC\TwigDocBundle\Tests\TestApp\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

trait ConfigurableContainerTrait
{
    public function createContainer(array $configs): ContainerInterface
    {
        $kernel = new Kernel(extraConfigs: $configs);
        $filesystem = new Filesystem();
        $filesystem->remove($kernel->getCacheDir());
        $kernel->boot();

        return $kernel->getContainer();
    }
}
