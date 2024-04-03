<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Cache;

use Psr\Cache\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

readonly class ComponentsWarmer implements CacheWarmerInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function isOptional(): bool
    {
        return true;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $componentService ??= $this->container->get('twig_doc.service.component');

        if ($componentService instanceof ComponentService) {
            $componentService->getComponents();
        }

        return [];
    }
}
