<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
class ComponentItemFactoryTest extends KernelTestCase
{
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
                'name' => 'InvalidComponentMissingTitleAndDescription',
                'category' => 'MainCategory',
                'title' => 'Component title',
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
}
