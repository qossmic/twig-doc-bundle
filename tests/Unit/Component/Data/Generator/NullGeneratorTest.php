<?php

namespace Qossmic\TwigDocBundle\Tests\Unit\Component\Data\Generator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Component\Data\Generator\NullGenerator;

#[CoversClass(NullGenerator::class)]
class NullGeneratorTest extends TestCase
{
    public function testSupports(): void
    {
        $generator = new NullGenerator();

        static::assertTrue($generator->supports('any', null));
        static::assertFalse($generator->supports('any', 'notEmpty'));
    }

    public function testGenerate(): void
    {
        $generator = new NullGenerator();

        static::assertNull($generator->generate('any'));
    }
}
