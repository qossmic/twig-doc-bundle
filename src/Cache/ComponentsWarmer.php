<?php

namespace Qossmic\TwigDocBundle\Cache;

use Psr\Container\ContainerInterface;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class ComponentsWarmer implements CacheWarmerInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function isOptional(): bool
    {
        return true;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $componentService ??= $this->container->get('twig_doc.service.component');

        if ($componentService instanceof ComponentService) {
            $componentService->getComponents();
        }

        return [];
    }
}
