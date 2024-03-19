<?php

namespace Qossmic\TwigDocBundle\Configuration;

interface ParserInterface
{
    public function parse(string $data): array;
}
