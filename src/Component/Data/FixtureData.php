<?php

namespace Qossmic\TwigDocBundle\Component\Data;

use Symfony\Component\PropertyInfo\Type;

/**
 * @codeCoverageIgnore
 */
class FixtureData
{
    public function __construct(
        public readonly string $className,
        /** @param array<string, Type> $properties */
        public readonly array  $properties,
        public readonly array  $params = []
    ) {
    }
}
