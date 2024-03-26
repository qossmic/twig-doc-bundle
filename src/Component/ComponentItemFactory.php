<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component;

use Qossmic\TwigDocBundle\Component\Data\Faker;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ComponentItemFactory
{
    private Faker $faker;

    public function __construct(private readonly ValidatorInterface $validator, private readonly CategoryService $categoryService)
    {
        $this->faker = new Faker();
    }

    /**
     * @throws InvalidComponentConfigurationException
     */
    public function create(array $data): ComponentItem
    {
        $item = $this->createItem($data);

        $category = $this->categoryService->getCategory($data['category'], $data['sub_category'] ?? null);

        if ($category === null) {
            $violations = ConstraintViolationList::createFromMessage(
                sprintf(
                    'invalid %s "%s". Valid categories are: %s. Valid sub-categories are: %s',
                    isset($data['sub_category']) ? 'sub_category' : 'category',
                    $data['sub_category'] ?? $data['category'],
                    implode(', ', array_keys($this->categoryService->getCategories())),
                    implode(', ', array_map(fn (ComponentCategory $category) => $category->getName(), $this->categoryService->getSubCategories()))
                )
            );
            throw new InvalidComponentConfigurationException($violations);
        }

        $item->setCategory($category);

        $violations = $this->validator->validate($item);

        if ($violations->count() > 0) {
            throw new InvalidComponentConfigurationException($violations);
        }

        return $item;
    }

    /**
     * @throws InvalidComponentConfigurationException
     */
    private function createItem(array $data): ComponentItem
    {
        $item = new ComponentItem();
        $item->setName($data['name'] ?? '')
            ->setTitle($data['title'] ?? '')
            ->setDescription($data['description'] ?? '')
            ->setTags($data['tags'] ?? [])
            ->setParameters($data['parameters'] ?? [])
            ->setVariations(
                $this->parseVariations($data['variations'] ?? [], $data['parameters'] ?? [])
            )
            ->setProjectPath($data['path'] ?? '')
            ->setRenderPath($data['renderPath'] ?? '');

        return $item;
    }

    public function getParamsFromVariables(array $variables): array
    {
        $r = [];
        foreach ($variables as $dotted) {
            $keys = explode('.', $dotted);
            $c = &$r[array_shift($keys)];
            foreach ($keys as $key) {
                if (isset($c[$key]) && $c[$key] === true) {
                    $c[$key] = [];
                }
                $c = &$c[$key];
            }
            if ($c === null) {
                $c = 'Scalar';
            }
        }

        return $r;
    }

    /**
     * @throws InvalidComponentConfigurationException
     */
    private function parseVariations(?array $variations, ?array $parameters): array
    {
        if (!$parameters) {
            return ['default' => []];
        }

        if (!$variations) {
            return [
                'default' => $this->faker->getFakeData($parameters),
            ];
        }

        $result = [];

        foreach ($variations as $variationName => $variationParams) {
            if (!\is_array($variationParams)) {
                throw new InvalidComponentConfigurationException(ConstraintViolationList::createFromMessage(sprintf('A component variation must contain an array of parameters. Variation Name: %s', $variationName)));
            }
            $result[$variationName] = $this->faker->getFakeData($parameters, $variationParams);
        }

        return $result;
    }
}
