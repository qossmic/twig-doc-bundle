<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Service;

use Qossmic\TwigDocBundle\Component\ComponentInvalid;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;

class ComponentService
{
    /**
     * @var ComponentItem[]
     */
    private array $components = [];

    /**
     * @var array<string, array<int, ComponentItem>>
     */
    private array $categories = [];

    /**
     * @var ComponentInvalid[]
     */
    private array $invalidComponents = [];

    public function __construct(
        private readonly ComponentItemFactory $itemFactory,
        private readonly array $componentsConfig,
    )
    {
        $this->parse();
    }

    /**
     * @return ComponentItem[]
     */
    public function getComponentsByCategory(string $category): array
    {
        return $this->categories[$category] ?? [];
    }

    /**
     * @return array<string, array<int, ComponentItem>>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    private function parse(): void
    {
        $components = $categories = $invalidComponents = [];

        foreach ($this->componentsConfig as $componentData) {
            try {
                $item = $this->itemFactory->create($componentData);
            } catch (InvalidComponentConfigurationException $e) {
                $item = new ComponentInvalid($e->getViolationList(), $componentData);
                $invalidComponents[] = $item;
                continue;
            }
            $components[] = $item;
            $categories[$item->getMainCategory()->getName()][] = $item;
        }

        $this->components = $components;
        $this->categories = $categories;
        $this->invalidComponents = $invalidComponents;
    }

    public function filter(string $filterQuery, string $filterType): array
    {
        $components = array_unique($this->filterComponents($filterQuery, $filterType), SORT_REGULAR);

        $result = [];

        foreach ($components as $component) {
            $result[$component->getMainCategory()->getName()][] = $component;
        }

        return $result;
    }

    private function filterComponents(string $filterQuery, string $filterType): array
    {
        $components = [];
        switch($filterType) {
            case 'category':
                $components = array_filter($this->categories, fn (string $category) => strtolower($category) === strtolower($filterQuery), ARRAY_FILTER_USE_KEY);

                return $components[array_key_first($components)] ?? [];
            case 'sub_category':
                $components = array_filter(
                    $this->components,
                    fn (ComponentItem $item) =>
                        $item->getCategory()->getParent() !== null
                        && strtolower($item->getCategory()->getName()) === strtolower($filterQuery)
                );

                break;
            case 'tags':
                $tags = array_map('trim', explode(',', strtolower($filterQuery)));
                $components = array_filter($this->components, function(ComponentItem $item) use ($tags) {
                    return array_intersect($tags, array_map('strtolower', $item->getTags())) !== [];
                });

                break;
            case 'name':
                $components = array_filter(
                    $this->components,
                    fn (ComponentItem $item) => str_contains(strtolower($item->getName()), strtolower($filterQuery)));

                break;
            default:
                foreach (['category', 'sub_category', 'tags', 'name'] as $type) {
                    $components = array_merge($components, $this->filterComponents($filterQuery, $type));
                }

                break;
        }

        return $components;
    }

    /**
     * @return ComponentItem[]
     */
    public function getInvalidComponents(): array
    {
        return $this->invalidComponents;
    }
}
