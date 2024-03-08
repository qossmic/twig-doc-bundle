<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Component;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Qossmic\TwigDocBundle\Service\CategoryService;

class ComponentItemFactory
{
    public function __construct(private readonly ValidatorInterface $validator, private readonly CategoryService $categoryService)
    {
    }

    public function create(array $data): ComponentItem
    {
        $item = $this->createItem($data);

        $category = $this->categoryService->getCategory($data['category'], $data['sub_category'] ?? null);

        if ($category === null) {
            $violations = ConstraintViolationList::createFromMessage(
                sprintf(
                    "invalid %s \"%s\". Valid categories are: %s. Valid sub-categories are: %s",
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

    private function createItem(array $data): ComponentItem
    {
        $item = new ComponentItem();
        $item->setName($data['name'] ?? '')
            ->setTitle($data['title'] ?? '')
            ->setDescription($data['description'] ?? '')
            ->setTags($data['tags'] ?? [])
            ->setParameters($data['parameters'] ?? [])
            ->setVariations($data['variations'] ?? [
                'default' => $this->createVariationParameters($data['parameters'] ?? [])
            ])
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

    public function createVariationParameters(array $parameters): array
    {
        $params = [];
        foreach ($parameters as $name => $type) {
            if (is_array($type)) {
                $paramValue = $this->createVariationParameters($type);
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
                return 'Hello World';
            case 'int':
            case 'integer':
                return random_int(0, 100000);
            case 'bool':
            case 'boolean':
                return [true, false][rand(0,1)];
            case 'float':
            case 'double':
                return (float) rand(1, 1000) / 100;
        }
    }
}
