<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

#[CoversClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
class ComponentItemFactoryTest extends KernelTestCase
{
    #[DataProvider('getValidComponents')]
    public function testValidComponent(array $componentData): void
    {
        $validator = static::getContainer()->get(ValidatorInterface::class);
        $categoryService = static::getContainer()->get(CategoryService::class);
        $componentItemFactory = new ComponentItemFactory($validator, $categoryService);

        $item = $componentItemFactory->create($componentData);

        static::assertInstanceOf(ComponentItem::class, $item);
        static::assertInstanceOf(ComponentCategory::class, $item->getCategory());
    }

    #[DataProvider('getInvalidComponentConfigurationTestCases')]
    public function testInvalidComponentConfiguration(array $componentData, string $expectedExceptionClass = InvalidComponentConfigurationException::class)
    {
        $service = new ComponentItemFactory(
            static::getContainer()->get('validator'),
            static::getContainer()->get('twig_doc.service.category')
        );

        self::expectException($expectedExceptionClass);

        $service->create($componentData);
    }

    public static function getInvalidComponentConfigurationTestCases(): iterable
    {
        yield [
            [
                'name' => 'InvalidComponent1',
                'category' => 'MainCategory'
            ]
        ];

        yield [
            [
                'name' => 'InvalidComponentMissingDescription',
                'category' => 'MainCategory',
                'title' => 'Component title'
            ]
        ];

        yield [
            [
                'name' => 'InvalidComponentWrongArrayConfigs',
                'category' => 'MainCategory',
                'title' => 'Component title',
                'description' => 'Component description',
                'parameters' => 'Should be an array',
                'variations' => 'Should be an array',
                'tags' => 'Should be an array',
            ],
            TypeError::class,
        ];
    }

    public static function getValidComponents(): iterable
    {
        yield 'Component without sub-category' => [
            [
                'name' => 'Component1',
                'title' => 'Component',
                'description' => 'Test component',
                'category' => 'MainCategory',
                'path' => 'path/to/template',
                'renderPath' => 'render/path/to/template',
                'parameters' => [],
                'variations' => [
                    'default' => []
                ]
            ],
        ];

        yield 'Component with sub-category' => [
            [
                'name' => 'Component1',
                'title' => 'Component',
                'description' => 'Test component',
                'category' => 'MainCategory',
                'sub_category' => 'SubCategory1',
                'path' => 'path/to/template',
                'renderPath' => 'render/path/to/template',
                'parameters' => [],
                'variations' => [
                    'default' => []
                ]
            ],
        ];
    }
}
