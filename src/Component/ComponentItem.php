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
    #[Assert\Length(max: 4096)]
    #[Assert\NotBlank]
    private string $projectPath;
    #[Assert\Length(max: 4096)]
    #[Assert\NotBlank]
    private string $renderPath;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function setVariations(array $variations): self
    {
        $this->variations = $variations;

        return $this;
    }

    public function addParameter(string $name, mixed $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function removeParameter(string $name): self
    {
        unset($this->parameters[$name]);

        return $this;
    }

    public function getVariations(): array
    {
        return $this->variations;
    }

    public function addVariation(string $name, array $variation): self
    {
        $this->variations[$name] = $variation;

        return $this;
    }

    public function removeVariation(string $name): self
    {
        unset($this->variations[$name]);

        return $this;
    }

    public function getCategory(): ComponentCategory
    {
        return $this->category;
    }

    public function setCategory(ComponentCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMainCategory(): ComponentCategory
    {
        return $this->category->getParent() ?? $this->category;
    }

    public function getProjectPath(): ?string
    {
        return $this->projectPath;
    }

    public function setProjectPath(?string $projectPath): self
    {
        $this->projectPath = $projectPath;

        return $this;
    }

    public function getRenderPath(): ?string
    {
        return $this->renderPath;
    }

    public function setRenderPath(?string $renderPath): self
    {
        $this->renderPath = $renderPath;

        return $this;
    }
}
