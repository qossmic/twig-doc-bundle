<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component\Data\Generator;

use Qossmic\TwigDocBundle\Component\Data\GeneratorInterface;

class NullGenerator implements GeneratorInterface
{
    public function supports(string $type, mixed $context = null): bool
    {
        return true;
    }

    public function generate(string $type, mixed $context = null): null
    {
        return null;
    }
}
