<?php

namespace Qossmic\TwigDocBundle\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Exception\InvalidConfigException;
use Qossmic\TwigDocBundle\Service\CategoryService;

#[CoversClass(CategoryService::class)]
#[UsesClass(ComponentCategory::class)]
class CategoryServiceTest extends TestCase
{
    #[DataProvider('getValidCategories')]
    public function testValidCategories(array $categoryConfig): void
    {
        $categoryService = new CategoryService([$categoryConfig]);

        static::assertInstanceOf(ComponentCategory::class, $categoryService->getCategory($categoryConfig['name']));
        static::assertCount(count($categoryConfig['sub_categories'] ?? []), $categoryService->getSubCategories());

        foreach ($categoryConfig['sub_categories'] ?? [] as $subCategoryName) {
            $subCategory = $categoryService->getCategory($categoryConfig['name'], $subCategoryName);

            static::assertEquals($subCategoryName, $subCategory->getName());
            static::assertEquals($categoryConfig['name'], $subCategory->getParent()->getName());
        }
    }

    public function testInvalidCategoryConfig()
    {
        $config = [
            [
                'name' => 'Category',
            ],
            [
                'name' => 'Category',
            ]
        ];

        static::expectException(InvalidConfigException::class);

        new CategoryService($config);
    }

    public function testGetCategories()
    {
        $service = new CategoryService([['name' => 'Category']]);

        $categories = $service->getCategories();

        static::assertCount(1, $categories);
        static::assertContainsOnlyInstancesOf(ComponentCategory::class, $categories);
    }

    public function testGetCategoryReturnsNullForUnknownCategory()
    {
        $service = new CategoryService([['name' => 'Category']]);

        static::assertNull($service->getCategory('NotExistingCategory'));
        static::assertNull($service->getCategory('Category', 'NotExistingSubCategory'));
    }

    public function testGetSubCategoriesForCategory(): void
    {
        $service = new CategoryService([
            [
                'name' => 'Category',
                'sub_categories' => [
                    'subCategory1',
                    'subCategory2',
                ]
            ],
            [
                'name' => 'Category2',
                'sub_categories' => [
                    'subCategory1',
                    'subCategory2',
                ]
            ],
        ]);

        $subCategories = $service->getSubCategories($service->getCategory('Category'));

        static::assertContainsOnlyInstancesOf(ComponentCategory::class, $subCategories);
        static::assertCount(2, $subCategories);
    }

    public static function getValidCategories(): iterable
    {
        yield 'Main Category' => [
            [
                'name' => 'MainCategory',
            ]
        ];

        yield 'Single sub category' => [
            [
                'name' => 'MainCategory',
                'sub_categories' => [
                    'SubCategory',
                ],
            ]
        ];

        yield 'Multiple sub categories' => [
            [
                'name' => 'MainCategory',
                'sub_categories' => [
                    'SubCategory1',
                    'SubCategory2',
                ],
            ]
        ];
    }
}
