<?php

namespace Qossmic\TwigDocBundle\Tests\TestApp\Entity;

class Car
{
    private string $color = 'blue';

    private Car|Manufacturer $manufacturer;

    public $blubber;

    public function __construct(private readonly string $blub)
    {
    }

    public function getBlub(): string
    {
        return $this->blub;
    }

    public function getManufacturer(): ?Manufacturer
    {
        return $this->manufacturer;
    }

    public function setManufacturer(Manufacturer $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
