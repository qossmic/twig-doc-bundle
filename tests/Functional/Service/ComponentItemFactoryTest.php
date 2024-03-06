<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use TypeError;

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
}
