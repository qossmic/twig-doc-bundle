<?php

namespace Qossmic\TwigDocBundle\Tests\Unit\Component;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[CoversClass(ComponentItemFactory::class)]
#[UsesClass(InvalidComponentConfigurationException::class)]
class ComponentItemFactoryTest extends TestCase
{
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

    public function testGetParamsFromVariables(): void
    {
        $variables = [
            'var.separated.by.dots',
            'second',
            'third.param'
        ];

        $componentItemFactory = new ComponentItemFactory(
            static::createMock(ValidatorInterface::class),
            static::createMock(CategoryService::class)
        );

        $result = $componentItemFactory->getParamsFromVariables($variables);

        static::assertEquals([
            'var'=> [
                'separated' => [
                    'by' => [
                        'dots' => 'Scalar'
                    ]
                ]
            ],
            'second' => 'Scalar',
            'third' => [
                'param' => 'Scalar'
            ],
        ], $result);
    }
}
