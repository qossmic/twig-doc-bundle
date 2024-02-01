<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @codeCoverageIgnore
 */
class ComponentCategory
{
    private ?ComponentCategory $parent = null;

    #[Assert\Regex('/^\w+$/')]
    private string $name;

    public function getParent(): ?ComponentCategory
    {
        return $this->parent;
    }

    public function setParent(?ComponentCategory $parent): ComponentCategory
    {
        $this->parent = $parent;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ComponentCategory
    {
        $this->name = $name;
        return $this;
    }
}
