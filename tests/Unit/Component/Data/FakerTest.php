<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Tests\Unit\Component\Data;

use OpenSC\TwigDocBundle\Component\Data\Faker;
use OpenSC\TwigDocBundle\Component\Data\FixtureData;
use OpenSC\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use OpenSC\TwigDocBundle\Component\Data\Generator\NullGenerator;
use OpenSC\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use OpenSC\TwigDocBundle\Tests\TestApp\Entity\Car;
use OpenSC\TwigDocBundle\Tests\TestApp\Entity\Manufacturer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Faker::class)]
#[UsesClass(FixtureData::class)]
#[UsesClass(ScalarGenerator::class)]
#[UsesClass(FixtureGenerator::class)]
#[UsesClass(NullGenerator::class)]
class FakerTest extends TestCase
{
    public function testGetFakeDataWithoutVariationData(): void
    {
        $params = [
            'car' => Car::class,
            'text' => 'String',
            'complex' => [
                'string' => 'String',
                'manufacturer' => Manufacturer::class,
                'deeper' => [
                    'car' => Car::class,
                    'company' => 'String',
                    'someNumber' => 'int',
                    'someInteger' => 'integer',
                    'someBool' => 'bool',
                    'someBoolean' => 'Boolean',
                    'someFloat' => 'float',
                    'someDouble' => 'Double',
                    'someResource' => 'Resource',
                    'someNull' => 'null',
                ],
            ],
        ];

        $faker = new Faker([new ScalarGenerator(), new FixtureGenerator(), new NullGenerator()]);
        $result = $faker->getFakeData($params);

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertIsString($result['text']);
        static::assertIsString($result['complex']['string']);
        static::assertIsString($result['complex']['deeper']['company']);
        static::assertIsFloat($result['complex']['deeper']['someFloat']);
        static::assertIsFloat($result['complex']['deeper']['someDouble']);
        static::assertIsInt($result['complex']['deeper']['someNumber']);
        static::assertIsInt($result['complex']['deeper']['someInteger']);
        static::assertIsBool($result['complex']['deeper']['someBool']);
        static::assertIsBool($result['complex']['deeper']['someBoolean']);
        static::assertNull($result['complex']['deeper']['someResource']);
        static::assertNull($result['complex']['deeper']['someNull']);
        static::assertInstanceOf(Car::class, $result['car']);
        static::assertInstanceOf(Car::class, $result['complex']['deeper']['car']);
        static::assertInstanceOf(Manufacturer::class, $result['complex']['manufacturer']);
    }

    public function testGetFakeDataForVariation(): void
    {
        $params = [
            'car' => Car::class,
        ];
        $variation = [
            'car' => [
                'color' => 'pink',
                'manufacturer' => [
                    'name' => 'Toyota',
                ],
            ],
        ];

        $faker = new Faker([new ScalarGenerator(), new FixtureGenerator(), new NullGenerator()]);
        $result = $faker->getFakeData($params, $variation['car']);

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertInstanceOf(Car::class, $result['car']);
        static::assertInstanceOf(Manufacturer::class, $result['car']->getManufacturer());
        static::assertEquals('Toyota', $result['car']->getManufacturer()->getName());
        static::assertEquals('pink', $result['car']->getColor());
    }
}
