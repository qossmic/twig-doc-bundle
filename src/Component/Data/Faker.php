<?php

namespace Qossmic\TwigDocBundle\Component\Data;

use Faker\Factory;
use Faker\Generator;
use Nelmio\Alice\Loader\NativeLoader;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;

/**
 * Creates fake data to be used in variation display for components.
 *
 * Provide seed in constructor to get reproducible random values
 */
class Faker
{
    private PropertyInfoExtractor $extractor;
    private NativeLoader $loader;
    private Generator $generator;

    public function __construct()
    {
        $this->loader = new NativeLoader();
        $reflectionExtractor = new ReflectionExtractor();
        $this->extractor = new PropertyInfoExtractor(
            listExtractors: [$reflectionExtractor],
            typeExtractors: [$reflectionExtractor],
            accessExtractors: [$reflectionExtractor]
        );
        $this->generator = Factory::create();
    }

    public function getFakeData(array $params, array $variation = []): array
    {
        return $this->build($params, $variation);
    }

    private function collectClasses(array $params, array $variation = []): ?array
    {
        $classes = [];
        foreach ($params as $name => $type) {
            if (\is_array($type)) {
                $classes[$name] = $this->collectClasses($type, $variation[$name] ?? []);
            } elseif (class_exists($type)) {
                $propertyInfo = $this->getPropertyInfo($type);
                $classes[$name] = new FixtureData(
                    $type,
                    $propertyInfo,
                    $variation[$name] ?? []
                );
            }
        }

        return $classes;
    }

    /**
     * @param array<string, Type> $props
     */
    private function getFixtureParams(string $className, array $props = [], array $params = []): array
    {
        foreach ($props as $prop => $type) {
            if (!\array_key_exists($prop, $params) && $this->extractor->isWritable($className, $prop)) {
                $params[$prop] = $this->createParamValue($type->getBuiltinType());
            }
        }

        return $params;
    }

    private function getPropertyInfo(string $class): array
    {
        $properties = [];

        $props = $this->extractor->getProperties($class);

        foreach ($props as $propName) {
            $types = $this->extractor->getTypes($class, $propName);

            // consider only the first type (PropertyInfo docs are wrong about only phpDocExtractor returning more than one type)
            $type = $types[0] ?? null;

            if (!$type) {
                // ignore untyped properties at the moment
                continue;
            }
            $properties[$propName] = $type;
        }

        return $properties;
    }

    private function build(array $params, array $variation = []): array
    {
        $fixturesToBuild = $this->collectClasses($params, $variation);
        $scalarParams = $this->createVariationParameters($this->arrayDiffKeyRecursive($params, $fixturesToBuild), $variation);

        $fixtures = $this->buildFixtures($fixturesToBuild);

        return array_merge_recursive($scalarParams, $fixtures);
    }

    private function buildFixtures(array $fixtures): array
    {
        $result = [];

        foreach ($fixtures as $name => $data) {
            if ($data instanceof FixtureData) {
                $result[$name] = $this->getFixture($name, $data);
            } else {
                $result[$name] = $this->buildFixtures($data);
            }
        }

        return $result;
    }

    private function getFixture(string $fixtureSetName, FixtureData $data): object
    {
        $otherFixtures = [];
        $fixtureParams = $data->params;

        foreach ($data->properties as $prop => $type) {
            if ($type->getBuiltinType() === Type::BUILTIN_TYPE_OBJECT) {
                $otherFixtures[$type->getClassName()] = [
                    $prop => array_merge(
                        $data->params[$prop]
                            ?? $this->getFixtureParams($type->getClassName(), $this->getPropertyInfo($type->getClassName())),
                        ['__construct' => false]
                    ),
                ];
                $fixtureParams[$prop] = sprintf('@%s', $prop);
            }
        }

        $fixtureData = array_merge($otherFixtures, [
            $data->className => [
                $fixtureSetName => array_merge($this->getFixtureParams($data->className, $data->properties, $fixtureParams), [
                    // disable constructor until we have time to fake constructor arguments :-)
                    '__construct' => false,
                ]),
            ],
        ]);

        return $this->loader->loadData($fixtureData)->getObjects()[$fixtureSetName];
    }

    public function createVariationParameters(array $parameters, array $variation): array
    {
        $params = [];
        foreach ($parameters as $name => $type) {
            if (\is_array($type)) {
                $paramValue = $this->createVariationParameters($type, $variation[$name] ?? []);
            } elseif (\array_key_exists($name, $variation)) {
                $paramValue = $variation[$name];
            } else {
                $paramValue = $this->createParamValue($type);
            }
            $params[$name] = $paramValue;
        }

        return $params;
    }

    private function createParamValue(string $type): bool|int|float|string|null
    {
        switch (strtolower($type)) {
            default:
                return null;
            case 'string':
                return $this->generator->text(20);
            case 'int':
            case 'integer':
                return $this->generator->numberBetween(0, 100000);
            case 'bool':
            case 'boolean':
                return [true, false][rand(0, 1)];
            case 'float':
            case 'double':
                return $this->generator->randomFloat();
        }
    }

    private function arrayDiffKeyRecursive(array $arr1, array $arr2): array
    {
        $diff = array_diff_key($arr1, $arr2);
        $intersect = array_intersect_key($arr1, $arr2);

        foreach ($intersect as $k => $v) {
            if (\is_array($arr1[$k]) && \is_array($arr2[$k])) {
                $d = $this->arrayDiffKeyRecursive($arr1[$k], $arr2[$k]);

                if ($d) {
                    $diff[$k] = $d;
                }
            }
        }

        return $diff;
    }
}
