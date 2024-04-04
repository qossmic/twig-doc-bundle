<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component\Data;

use Symfony\Component\PropertyInfo\Type;

/**
 * @codeCoverageIgnore
 */
readonly class FixtureData
{
    public function __construct(
        public string $className,
        /** @param array<string, Type> $properties */
        public array $properties,
        public array $params = []
    ) {
    }
}
