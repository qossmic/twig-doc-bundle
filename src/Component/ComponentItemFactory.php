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
                    implode(', ', array_keys($this->categoryService->getSubCategories()))
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
            ->setVariations($data['variations'] ?? []);

        return $item;
    }
}
