<?php

namespace Qossmic\TwigDocBundle\Component;

/**
 * @method ComponentItem[] getArrayCopy()
 */
class ComponentItemList extends \ArrayObject
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    private array $sortableFields = [
        'name',
        'category',
        'title',
    ];

    /**
     * @param ComponentItem[] $items
     */
    public function __construct(array $items)
    {
        parent::__construct($items);
    }

    /**
     * @return ComponentItem[]
     */
    public function paginate(int $start = 0, int $limit = 15): array
    {
        return \array_slice($this->getArrayCopy(), $start, $limit);
    }

    public function sort(string $field, string $direction = self::SORT_ASC): void
    {
        if (!\in_array($field, $this->sortableFields)) {
            throw new \InvalidArgumentException(sprintf('field "%s" is not sortable', $field));
        }

        $method = sprintf('get%s', ucfirst($field));

        $this->uasort(function (ComponentItem $item, ComponentItem $item2) use ($method, $direction) {
            if ($direction === self::SORT_DESC) {
                return \call_user_func([$item2, $method]) <=> \call_user_func([$item, $method]);
            }

            return \call_user_func([$item, $method]) <=> \call_user_func([$item2, $method]);
        });
    }

    public function filter(string $query, ?string $type): self
    {
        $components = [];
        switch ($type) {
            case 'category':
                $components = array_filter(
                    $this->getArrayCopy(),
                    function (ComponentItem $item) use ($query) {
                        $category = $item->getCategory()->getName();
                        $parent = $item->getCategory()->getParent();
                        while ($parent !== null) {
                            $category = $parent->getName();
                            $parent = $parent->getParent();
                        }

                        return strtolower($category) === strtolower($query);
                    }
                );

                break;
            case 'sub_category':
                $components = array_filter(
                    $this->getArrayCopy(),
                    fn (ComponentItem $item) => $item->getCategory()->getParent() !== null
                        && strtolower($item->getCategory()->getName()) === strtolower($query)
                );

                break;
            case 'tags':
                $tags = array_map('trim', explode(',', strtolower($query)));
                $components = array_filter($this->getArrayCopy(), function (ComponentItem $item) use ($tags) {
                    return array_intersect($tags, array_map('strtolower', $item->getTags())) !== [];
                });

                break;
            case 'name':
                $components = array_filter(
                    $this->getArrayCopy(),
                    fn (ComponentItem $item) => str_contains(strtolower($item->getName()), strtolower($query))
                );

                break;
            default:
                foreach (['category', 'sub_category', 'tags', 'name'] as $type) {
                    $components = array_merge($components, (array) $this->filter($query, $type));
                }

                break;
        }

        return new self(array_unique($components, \SORT_REGULAR));
    }
}
