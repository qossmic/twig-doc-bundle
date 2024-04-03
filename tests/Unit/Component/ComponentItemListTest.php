<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Unit\Component;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemList;

#[CoversClass(ComponentItemList::class)]
class ComponentItemListTest extends TestCase
{
    public function testPaginate(): void
    {
        $items = [];
        for ($i = 0; $i < 100; ++$i) {
            $item = new ComponentItem();
            $item->setName('name'.$i)
                ->setCategory((new ComponentCategory())->setName('category'))
                ->setTitle('title')
                ->setDescription('description');
            $items[] = $item;
        }

        $list = new ComponentItemList($items);

        $paginated = $list->paginate(90, 10);

        static::assertCount(10, $paginated);

        $paginated = $list->paginate(95, 10);

        static::assertCount(5, $paginated);
    }

    #[DataProvider('getSortTestCases')]
    public function testSort(string $field, string $direction, string $expectedPropertyValue): void
    {
        $items = [];
        for ($i = 0; $i < 100; ++$i) {
            $item = new ComponentItem();
            $item->setName('name'.$i)
                ->setCategory((new ComponentCategory())->setName('category'.$i))
                ->setTitle('title'.$i)
                ->setDescription('description'.$i);
            $items[] = $item;
        }

        $list = new ComponentItemList($items);

        $list->sort($field, $direction);

        $items = $list->paginate();

        static::assertEquals($expectedPropertyValue, \call_user_func([$items[0], sprintf('get%s', ucfirst($field))]));
    }

    #[DataProvider('getFilterTestCases')]
    public function testFilter(array $components, string $query, ?string $type, int $expectedCount): void
    {
        $list = new ComponentItemList($components);

        $filtered = $list->filter($query, $type);

        static::assertCount($expectedCount, $filtered);
    }

    #[DataProvider('getInvalidFields')]
    public function testSortThrowsInvalidArgumentExceptionForNonSortableFields(string $field): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $list = new ComponentItemList([]);

        $list->sort($field);
    }

    public static function getSortTestCases(): iterable
    {
        yield 'name ASC' => [
            'name',
            ComponentItemList::SORT_ASC,
            'name0',
        ];

        yield 'name DESC' => [
            'name',
            ComponentItemList::SORT_DESC,
            'name99',
        ];

        yield 'category ASC' => [
            'category',
            ComponentItemList::SORT_ASC,
            'category0',
        ];

        yield 'category DESC' => [
            'category',
            ComponentItemList::SORT_DESC,
            'category99',
        ];
    }

    public static function getInvalidFields(): array
    {
        return [
            ['description'],
            ['tags'],
            ['parameters'],
            ['variations'],
        ];
    }

    public static function getFilterTestCases(): iterable
    {
        yield 'case insensitive, like match by name' => [
            'components' => [
                (new ComponentItem())
                    ->setName('Component'),
                (new ComponentItem())
                    ->setName('button'),
                (new ComponentItem())
                    ->setName('ShinyComponent'),
            ],
            'query' => 'component',
            'type' => 'name',
            'expectedCount' => 2,
        ];

        yield 'case insensitive, exact match by category' => [
            'components' => [
                (new ComponentItem())
                    ->setCategory((new ComponentCategory())->setName('Category')),
                (new ComponentItem())
                    ->setCategory((new ComponentCategory())->setName('Category2')),
            ],
            'query' => 'category',
            'type' => 'category',
            'expectedCount' => 1,
        ];

        yield 'case insensitive, exact match by sub-category' => [
            'components' => [
                (new ComponentItem())
                    ->setCategory(
                        (new ComponentCategory())->setName('Category')
                            ->setParent((new ComponentCategory())->setName('ParentCategory'))
                    ),
                (new ComponentItem())
                    ->setCategory((new ComponentCategory())->setName('Category')),
            ],
            'query' => 'category',
            'type' => 'sub_category',
            'expectedCount' => 1,
        ];

        yield 'case insensitive, exact match by tags' => [
            'components' => [
                (new ComponentItem())
                    ->setName('cmp1')
                    ->setTags(['tag1', 'tag2', 'tag3']),
                (new ComponentItem())
                    ->setName('cmp2')
                    ->setTags(['tag1', 'tag2', 'tag3']),
                (new ComponentItem())
                    ->setName('cmp3')
                    ->setTags(['tag4', 'tag5', 'tag6']),
                (new ComponentItem())
                    ->setName('cmp4')
                    ->setTags(['tag7', 'tag8', 'tag9']),
            ],
            'query' => 'tag1, Tag2, TAG3',
            'type' => 'tags',
            'expectedCount' => 2,
        ];

        yield 'search all fields' => [
            'components' => [
                (new ComponentItem())
                    ->setName('cmp1')
                    ->setTags(['tag1', 'tag2', 'tag3'])
                    ->setCategory(
                        (new ComponentCategory())->setName('Category')
                            ->setParent((new ComponentCategory())->setName('Anything'))
                    ),
                (new ComponentItem())
                    ->setName('cmpTag2')
                    ->setTags(['tag1', 'tag2', 'anything'])
                    ->setCategory((new ComponentCategory())->setName('Category')),
                (new ComponentItem())
                    ->setName('cmpAnything3')
                    ->setTags(['tag4', 'tag5', 'tag6'])
                    ->setCategory((new ComponentCategory())->setName('Category')),
                (new ComponentItem())
                    ->setName('cmp4')
                    ->setTags(['tag', 'tag8', 'tag9'])
                    ->setCategory((new ComponentCategory())->setName('Category')),
            ],
            'query' => 'anything',
            'type' => null,
            'expectedCount' => 3,
        ];
    }
}
