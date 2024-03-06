<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentInvalid;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(ComponentService::class)]
#[UsesClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
class ComponentServiceTest extends KernelTestCase
{
    #[DataProvider('getFilterTestCases')]
    public function testFilter(string $query, string $type, array $expectedCounts): void
    {
        $service = static::getContainer()->get(ComponentService::class);

        $components = $service->filter($query, $type);

        foreach ($expectedCounts as $category => $count) {
            static::assertCount($count, $components[$category]);
        }
    }

    public function testGetComponent(): void
    {
        $service = static::getContainer()->get(ComponentService::class);

        $component = $service->getComponent('ButtonAction');

        static::assertInstanceOf(ComponentItem::class, $component);
        static::assertEquals('ButtonAction', $component->getName());
    }

    public function testGetComponentsByCategory(): void
    {
        $service = static::getContainer()->get(ComponentService::class);

        $result = $service->getComponentsByCategory('MainCategory');

        static::assertCount(4, $result);
    }

    public function testGetCategories()
    {
        $service = static::getContainer()->get(ComponentService::class);

        $categories = $service->getCategories();

        static::assertCount(1, $categories);
    }

    public function testGetInvalidComponents()
    {
        $service = static::getContainer()->get(ComponentService::class);

        $invalid = $service->getInvalidComponents();

        static::assertCount(1, $invalid);
        foreach ($invalid as $component) {
            static::assertInstanceOf(ComponentInvalid::class, $component);
        }
    }

    public static function getFilterTestCases(): iterable
    {
        yield 'name' => [
            'query' => 'button',
            'type' => 'name',
            'expectedCounts' => [
                'MainCategory' => 3
            ]
        ];

        yield 'category' => [
            'query' => 'MainCategory',
            'type' => 'category',
            'expectedCounts' => [
                'MainCategory' => 4
            ]
        ];

        yield 'sub_category' => [
            'query' => 'SubCategory2',
            'type' => 'sub_category',
            'expectedCounts' => [
                'MainCategory' => 1
            ]
        ];

        yield 'tags' => [
            'query' => 'snippet',
            'type' => 'tags',
            'expectedCounts' => [
                'MainCategory' => 1
            ]
        ];

        yield 'any' => [
            'query' => 'action',
            'type' => '',
            'expectedCounts' => [
                'MainCategory' => 1
            ]
        ];
    }
}
