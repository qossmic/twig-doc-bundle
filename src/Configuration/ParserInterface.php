<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Configuration;

interface ParserInterface
{
    public function parse(string $data): array;
}
