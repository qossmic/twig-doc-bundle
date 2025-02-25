<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Component\Data;

interface GeneratorInterface
{
    public function supports(string $type, mixed $context = null): bool;

    public function generate(string $type, mixed $context = null);
}
