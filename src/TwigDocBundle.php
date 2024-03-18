<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle;

use Qossmic\TwigDocBundle\Configuration\YamlParser;
use Qossmic\TwigDocBundle\DependencyInjection\Compiler\TwigDocCollectDocsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TwigDocBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TwigDocCollectDocsPass(new YamlParser()));
    }

    public function getPath(): string
    {
        return __DIR__.'/..';
    }
}
