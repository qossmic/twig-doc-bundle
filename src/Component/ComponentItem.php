<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @codeCoverageIgnore
 */
class ComponentItem
{
    #[Assert\NotBlank]
    private string $name;
    #[Assert\NotBlank]
    private string $title;
    #[Assert\NotBlank]
    private string $description;
    #[Assert\Type('array')]
    private array $tags;
    #[Assert\Type('array')]
    private array $parameters;
    #[Assert\Type('array')]
    private array $variations;
    #[Assert\Valid]
    private ComponentCategory $category;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ComponentItem
    {
        $this->name = $name;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): ComponentItem
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ComponentItem
    {
        $this->description = $description;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): ComponentItem
    {
        $this->tags = $tags;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): ComponentItem
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function setVariations(array $variations): ComponentItem
    {
        $this->variations = $variations;

        return $this;
    }

    public function addParameter(string $name, mixed $value): ComponentItem
    {
        $this->parameters[$name] = $value;
        return $this;
    }

    public function removeParameter(string $name): ComponentItem
    {
        unset($this->parameters[$name]);

        return $this;
    }

    public function getVariations(): array
    {
        return $this->variations;
    }

    public function addVariation(string $name, array $variation): ComponentItem
    {
        $this->variations[$name] = $variation;
        return $this;
    }

    public function removeVariation(string $name): ComponentItem
    {
        unset($this->variations[$name]);

        return $this;
    }

    public function getCategory(): ComponentCategory
    {
        return $this->category;
    }

    public function setCategory(ComponentCategory $category): ComponentItem
    {
        $this->category = $category;
        return $this;
    }

    public function getMainCategory(): ComponentCategory
    {
        return $this->category->getParent() ?? $this->category;
    }
}
