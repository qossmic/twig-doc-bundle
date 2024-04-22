<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component\Data\Generator;

use Faker\Factory;
use Faker\Generator;
use Qossmic\TwigDocBundle\Component\Data\GeneratorInterface;
use Symfony\Component\PropertyInfo\Type;

class ScalarGenerator implements GeneratorInterface
{
    private Generator $generator;

    public function __construct()
    {
        $this->generator = Factory::create();
    }

    public function supports(string $type, mixed $context = null): bool
    {
        // context normally contains the param values for a specific variation, so we generate random values only for non-set params
        return null === $context && \in_array(strtolower($type), [
            Type::BUILTIN_TYPE_BOOL,
            Type::BUILTIN_TYPE_FLOAT,
            Type::BUILTIN_TYPE_INT,
            Type::BUILTIN_TYPE_NULL,
            Type::BUILTIN_TYPE_STRING,
            'integer',
            'double',
            'boolean',
        ], true);
    }

    public function generate(string $type, mixed $context = null): float|object|bool|int|string|null
    {
        return $this->createParamValue($type);
    }

    public function createParamValue(string $type): bool|int|float|string|null
    {
        switch (strtolower($type)) {
            case 'string':
                return $this->generator->text(20);
            case 'int':
            case 'integer':
                return $this->generator->numberBetween(0, 100000);
            case 'bool':
            case 'boolean':
                return [true, false][random_int(0, 1)];
            case 'float':
            case 'double':
                return $this->generator->randomFloat();
            default:
                return null;
        }
    }
}
