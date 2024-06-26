<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\TestApp\Entity;

class Special
{
    public function __construct(private readonly array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }
}
