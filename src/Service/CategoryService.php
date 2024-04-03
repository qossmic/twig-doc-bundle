<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Service;

use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Exception\InvalidConfigException;

class CategoryService
{
    /**
     * @var ComponentCategory[]
     */
    private array $categories = [];

    /**
     * @var ComponentCategory[]
     */
    private array $subCategories = [];

    public function __construct(private readonly array $categoriesConfig)
    {
        $this->parseCategories();
    }

    /**
     * @return ComponentCategory[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return ComponentCategory[]
     */
    public function getSubCategories(?ComponentCategory $mainCategory = null): array
    {
        if ($mainCategory !== null) {
            return array_filter($this->subCategories, static fn (ComponentCategory $category) => $category->getParent() === $mainCategory);
        }

        return $this->subCategories;
    }

    public function getCategory(string $category, ?string $subCategoryName = null): ?ComponentCategory
    {
        if ($subCategoryName === null) {
            return $this->categories[$category] ?? null;
        }

        foreach ($this->subCategories as $subCategory) {
            if ($subCategory->getName() === $subCategoryName && $subCategory->getParent()->getName() === $category) {
                return $subCategory;
            }
        }

        return null;
    }

    private function parseCategories(): void
    {
        foreach ($this->categoriesConfig as $category) {
            $cat = new ComponentCategory();
            $cat->setName($category['name']);

            if (isset($this->categories[$cat->getName()])) {
                throw new InvalidConfigException(sprintf('Category %s has been already configured, be sure to have main-categories only once', $cat->getName()));
            }
            $this->categories[$cat->getName()] = $cat;

            foreach ($category['sub_categories'] ?? [] as $subCategory) {
                $sub = new ComponentCategory();
                $sub->setParent($cat);
                $sub->setName($subCategory);

                $this->subCategories[] = $sub;
            }
        }
    }
}
