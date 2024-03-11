<?php

namespace Qossmic\TwigDocBundle\Tests\Unit\Component;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(ComponentItemFactory::class)]
#[UsesClass(InvalidComponentConfigurationException::class)]
class ComponentItemFactoryTest extends TestCase
{
    #[DataProvider('getValidComponents')]
    public function testValidComponent(array $componentData): void
    {
        $componentCategoryMock = $this->getComponentCategoryMock($componentData['category'], $componentData['sub_category'] ?? null);
        $categoryServiceMock = static::createMock(CategoryService::class);
        $categoryServiceMock
            ->method('getCategory')
            ->with($componentData['category'])
            ->willReturn($componentCategoryMock)
        ;
        $validatorMock = static::createMock(ValidatorInterface::class);
        $validatorMock->method('validate')
            ->willReturn(new ConstraintViolationList());

        $componentItemFactory = new ComponentItemFactory($validatorMock, $categoryServiceMock);

        $item = $componentItemFactory->create($componentData);

        static::assertInstanceOf(ComponentItem::class, $item);
        static::assertInstanceOf(ComponentCategory::class, $item->getCategory());
    }

    public function testInvalidCategory()
    {
        static::expectException(InvalidComponentConfigurationException::class);

        $categoryServiceMock = static::createMock(CategoryService::class);
        $categoryServiceMock
            ->method('getCategory')
            ->willReturn(null)
        ;
        $validatorMock = static::createMock(ValidatorInterface::class);

        $componentItemFactory = new ComponentItemFactory($validatorMock, $categoryServiceMock);

        $componentItemFactory->create(['category' => 'Category']);
    }

    public static function getValidComponents(): iterable
    {
        yield 'Component without sub-category' => [
            [
                'name' => 'Component1',
                'title' => 'Component',
                'description' => 'Test component',
                'category' => 'TestCategory',
                'parameters' => [],
                'variations' => [
                    'default' => [],
                ],
            ],
        ];

        yield 'Component with sub-category' => [
            [
                'name' => 'Component1',
                'title' => 'Component',
                'description' => 'Test component',
                'category' => 'TestCategory',
                'sub_category' => 'SubCategory',
                'parameters' => [],
                'variations' => [
                    'default' => [],
                ],
            ],
        ];
    }

    private function getComponentCategoryMock(string $category, ?string $subCategory = null): ComponentCategory
    {
        $componentCategoryMock = static::createMock(ComponentCategory::class);
        $componentCategoryMock->method('getName')
            ->willReturn($subCategory ?? $category);
        $parentMock = null;
        if ($subCategory) {
            $parentMock = static::createMock(ComponentCategory::class);
            $parentMock->method('getName')->willReturn($category);
        }
        $componentCategoryMock->method('getParent')
            ->willReturn($parentMock);

        return $componentCategoryMock;
    }
}
