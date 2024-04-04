<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Unit\Component\Data\Generator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use Qossmic\TwigDocBundle\Tests\TestApp\Entity\Car;
use Symfony\Component\PropertyInfo\Type;

#[CoversClass(ScalarGenerator::class)]
class ScalarGeneratorTest extends TestCase
{
    #[DataProvider('getSupportsTestCases')]
    public function testSupports(string $type, mixed $context, bool $expected): void
    {
        $generator = new ScalarGenerator();

        self::assertEquals($expected, $generator->supports($type, $context));
    }

    #[DataProvider('getGenerateTestCases')]
    public function testGenerate(string $type, string $assertionMethod): void
    {
        $generator = new ScalarGenerator();

        self::$assertionMethod($generator->generate($type));
    }

    public static function getSupportsTestCases(): iterable
    {
        yield 'existing class' => [
            'type' => Car::class,
            'context' => null,
            'expectedReturn' => false,
        ];

        yield 'non-empty context' => [
            'type' => 'String',
            'context' => [
                'value' => 'hello world',
            ],
            'expectedReturn' => false,
        ];

        yield 'php internal class' => [
            'type' => 'ArrayObject',
            'context' => null,
            'expectedReturn' => false,
        ];
    }

    public static function getGenerateTestCases(): iterable
    {
        yield 'bool' => [
            'type' => Type::BUILTIN_TYPE_BOOL,
            'method' => 'assertIsBool',
        ];

        yield 'boolean' => [
            'type' => 'boolean',
            'method' => 'assertIsBool',
        ];

        yield 'float' => [
            'type' => Type::BUILTIN_TYPE_FLOAT,
            'method' => 'assertIsFloat',
        ];

        yield 'double' => [
            'type' => 'double',
            'method' => 'assertIsFloat',
        ];

        yield 'integer' => [
            'type' => 'integer',
            'method' => 'assertIsInt',
        ];

        yield 'int' => [
            'type' => Type::BUILTIN_TYPE_INT,
            'method' => 'assertIsInt',
        ];

        yield 'null' => [
            'type' => Type::BUILTIN_TYPE_NULL,
            'method' => 'assertNull',
        ];

        yield 'string' => [
            'type' => Type::BUILTIN_TYPE_STRING,
            'method' => 'assertIsString',
        ];
    }
}
