<?php

namespace Qossmic\TwigDocBundle\Tests\TestApp\Entity;

class Manufacturer
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Manufacturer
    {
        $this->name = $name;

        return $this;
    }
}
