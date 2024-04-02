<?php

namespace Qossmic\TwigDocBundle\Component\Data;

interface GeneratorInterface
{
    public function supports(string $type, mixed $context = null): bool;

    public function generate(string $type, mixed $context = null);
}
