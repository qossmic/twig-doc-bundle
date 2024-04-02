<?php

namespace Qossmic\TwigDocBundle\Component\Data\Generator;

use Nelmio\Alice\Loader\NativeLoader;
use Qossmic\TwigDocBundle\Component\Data\FixtureData;
use Qossmic\TwigDocBundle\Component\Data\GeneratorInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;

class FixtureGenerator implements GeneratorInterface
{
    private NativeLoader $loader;
    private PropertyInfoExtractor $extractor;
    private ScalarGenerator $scalarGenerator;

    public function __construct()
    {
        $this->loader = new NativeLoader();
        $reflectionExtractor = new ReflectionExtractor();
        $this->extractor = new PropertyInfoExtractor(
            listExtractors: [$reflectionExtractor],
            typeExtractors: [$reflectionExtractor],
            accessExtractors: [$reflectionExtractor]
        );
        $this->scalarGenerator = new ScalarGenerator();
    }

    public function supports(string $type, mixed $context = null): bool
    {
        return class_exists($type)
            && ($context === null || \is_array($context));
    }

    public function generate(string $type, mixed $context = null): object
    {
        return $this->getFixture($type, new FixtureData(
            $type,
            $this->getPropertyInfo($type),
            (array) $context
        ));
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

    /**
     * @param array<string, Type> $props
     */
    private function getFixtureParams(string $className, array $props = [], array $params = []): array
    {
        foreach ($props as $prop => $type) {
            if (!\array_key_exists($prop, $params) && $this->extractor->isWritable($className, $prop)) {
                $params[$prop] = $this->scalarGenerator->createParamValue($type->getBuiltinType());
            }
        }

        return $params;
    }
}
