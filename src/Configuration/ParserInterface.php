<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Configuration;

interface ParserInterface
{
    public function parse(string $data): array;
}
