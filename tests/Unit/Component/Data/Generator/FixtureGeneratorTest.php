<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Unit\Component\Data\Generator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use Qossmic\TwigDocBundle\Tests\TestApp\Entity\Car;
use Qossmic\TwigDocBundle\Tests\TestApp\Entity\Manufacturer;

#[CoversClass(FixtureGenerator::class)]
#[UsesClass(ScalarGenerator::class)]
class FixtureGeneratorTest extends TestCase
{
    #[DataProvider('getSupportsTestCases')]
    public function testSupports(string $type, mixed $context, bool $expectedReturn): void
    {
        $generator = new FixtureGenerator();

        self::assertEquals($expectedReturn, $generator->supports($type, $context));
    }

    public function testGenerateByTypeWithoutContext(): void
    {
        $generator = new FixtureGenerator();

        $fixture = $generator->generate(Car::class);

        self::assertInstanceOf(Car::class, $fixture);
        self::assertInstanceOf(Manufacturer::class, $fixture->getManufacturer());
        self::assertNull($fixture->typeLessProperty);
        self::assertIsString($fixture->getColor());
    }

    public function testGenerateByTypeWithContext(): void
    {
        $generator = new FixtureGenerator();

        $fixture = $generator->generate(Car::class, [
            'color' => 'black',
            'manufacturer' => [
                'name' => 'Aston Martin',
            ],
        ]);

        self::assertInstanceOf(Car::class, $fixture);
        self::assertInstanceOf(Manufacturer::class, $fixture->getManufacturer());
        self::assertNull($fixture->typeLessProperty);
        self::assertEquals('black', $fixture->getColor());
        self::assertEquals('Aston Martin', $fixture->getManufacturer()->getName());
    }

    public static function getSupportsTestCases(): iterable
    {
        yield 'existing class' => [
            'type' => Car::class,
            'context' => null,
            'expectedReturn' => true,
        ];

        yield 'non-existing class' => [
            'type' => 'App\This\Class\Does\Not\Exist',
            'context' => null,
            'expectedReturn' => false,
        ];

        yield 'php internal class' => [
            'type' => 'ArrayObject',
            'context' => null,
            'expectedReturn' => true,
        ];

        yield 'valid context' => [
            'type' => Car::class,
            'context' => [
                'color' => 'green',
            ],
            'expectedReturn' => true,
        ];

        yield 'invalid context' => [
            'type' => Car::class,
            'context' => 42,
            'expectedReturn' => false,
        ];
    }
}
