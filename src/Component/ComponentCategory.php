<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @codeCoverageIgnore
 */
class ComponentCategory implements \Stringable
{
    public const DEFAULT_CATEGORY = 'Components';

    private ?ComponentCategory $parent = null;

    #[Assert\Regex('/^\w+$/')]
    private string $name;

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
