<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Component\Data\Faker;
use Qossmic\TwigDocBundle\Component\Data\FixtureData;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Tests\TestApp\Entity\Car;
use Qossmic\TwigDocBundle\Tests\TestApp\Entity\Manufacturer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
#[UsesClass(Faker::class)]
#[UsesClass(FixtureData::class)]
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

    public function testComponentWithoutParameters(): void
    {
        $data = [
            'name' => 'TestComponent',
            'title' => 'Test title',
            'description' => 'description',
            'category' => 'MainCategory',
            'path' => 'path/to/component',
            'renderPath' => 'path/to/component',
        ];

        /** @var ComponentItemFactory $factory */
        $factory = self::getContainer()->get('twig_doc.service.component_factory');

        $component = $factory->create($data);

        self::assertInstanceOf(ComponentItem::class, $component);
    }

    public function testFactoryCreatesDefaultVariationWhenMissingInConfig(): void
    {
        $data = [
            'name' => 'TestComponent',
            'title' => 'Test title',
            'description' => 'description',
            'category' => 'MainCategory',
            'path' => 'path/to/component',
            'renderPath' => 'path/to/component',
        ];

        /** @var ComponentItemFactory $factory */
        $factory = self::getContainer()->get('twig_doc.service.component_factory');

        $component = $factory->create($data);

        self::assertArrayHasKey('default', $component->getVariations());
    }

    public function testFactoryCreatesDefaultVariationWithParameterTypes(): void
    {
        $data = [
            'name' => 'TestComponent',
            'title' => 'Test title',
            'description' => 'description',
            'category' => 'MainCategory',
            'path' => 'path/to/component',
            'renderPath' => 'path/to/component',
            'parameters' => [
                'string' => 'String',
                'float' => 'Float',
                'double' => 'Double',
                'int' => 'Int',
                'integer' => 'Integer',
                'bool' => 'Bool',
                'boolean' => 'Boolean',
                'unknown' => 'CustomType',
                'complex' => [
                    'title' => 'String',
                    'amount' => 'Float',
                ],
            ],
        ];

        /** @var ComponentItemFactory $factory */
        $factory = self::getContainer()->get('twig_doc.service.component_factory');

        $component = $factory->create($data);

        self::assertInstanceOf(ComponentItem::class, $component);
        self::assertIsBool($component->getVariations()['default']['bool']);
        self::assertIsBool($component->getVariations()['default']['boolean']);
        self::assertIsString($component->getVariations()['default']['string']);
        self::assertIsInt($component->getVariations()['default']['int']);
        self::assertIsInt($component->getVariations()['default']['integer']);
        self::assertIsFloat($component->getVariations()['default']['float']);
        self::assertIsFloat($component->getVariations()['default']['double']);
        self::assertNull($component->getVariations()['default']['unknown']);

        self::assertIsArray($component->getVariations()['default']['complex']);
        self::assertIsString($component->getVariations()['default']['complex']['title']);
        self::assertIsFloat($component->getVariations()['default']['complex']['amount']);
    }

    public function testCreateForObjectParameter(): void
    {
        $data = [
            'name' => 'component',
            'title' => 'title',
            'description' => 'description',
            'category' => 'MainCategory',
            'path' => 'path',
            'renderPath' => 'renderPath',
            'parameters' => [
                'car' => Car::class
            ],
            'variations' => [
                'fuchsia' => [
                    'car' => [
                        'color' => 'fuchsia',
                        'manufacturer' => [
                            'name' => 'Mitsubishi',
                        ]
                    ]
                ]
            ]
        ];

        /** @var ComponentItemFactory $factory */
        $factory = self::getContainer()->get('twig_doc.service.component_factory');

        $item = $factory->create($data);
        $variations = $item->getVariations();

        static::assertInstanceOf(ComponentItem::class, $item);
        static::assertArrayHasKey('fuchsia', $variations);
        static::assertIsArray($variations['fuchsia']);
        static::assertArrayHasKey('car', $variations['fuchsia']);

        $car = $variations['fuchsia']['car'];

        static::assertInstanceOf(Car::class, $car);
        static::assertEquals('fuchsia', $car->getColor());
    }

    public static function getInvalidComponentConfigurationTestCases(): iterable
    {
        yield [
            [
                'name' => 'InvalidComponent1',
                'category' => 'MainCategory',
            ],
        ];

        yield [
            [
                'name' => 'InvalidComponentMissingDescription',
                'category' => 'MainCategory',
                'title' => 'Component title',
            ],
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
            \TypeError::class,
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
                    'default' => [],
                ],
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
                    'default' => [],
                ],
            ],
        ];
    }
}
