<?php

namespace Qossmic\TwigDocBundle\Component\Data;

/**
 * Creates fake data to be used in variation display for components.
 */
class Faker
{
    public function __construct(
        /**
         * @param GeneratorInterface[] $generators
         */
        private readonly iterable $generators
    ) {
    }

    public function getFakeData(array $params, mixed $variation = []): array
    {
        return $this->createFakeData($params, $variation);
    }

    private function createFakeData(array $params, mixed $variation): array
    {
        $result = [];

        foreach ($params as $name => $type) {
            if (\is_array($type)) {
                $result[$name] = $this->createFakeData($type, $variation[$name] ?? null);

                continue;
            }

            foreach ($this->generators as $generator) {
                if (\array_key_exists($name, $result) || !$generator->supports($type)) {
                    continue;
                }
                if ($generator->supports($type, $variation)) {
                    $result[$name] = $generator->generate($type, $variation);

                    break;
                }
            }

            if (!\array_key_exists($name, $result)) {
                // set from variation
                $result[$name] = $variation;
            }
        }

        return $result;
    }
}
