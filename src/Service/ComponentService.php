<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Service;

use Psr\Cache\InvalidArgumentException;
use Qossmic\TwigDocBundle\Component\ComponentInvalid;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Component\ComponentItemList;
use Qossmic\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Symfony\Contracts\Cache\CacheInterface;

class ComponentService
{
    public function __construct(
        private readonly ComponentItemFactory $itemFactory,
        private readonly array $componentsConfig,
        private readonly CacheInterface $cache,
        private readonly int $configReadTime = 0
    ) {
    }

    /**
     * @return ComponentItemList<ComponentItem>
     */
    public function getComponentsByCategory(string $category): ComponentItemList
    {
        return $this->filter($category, 'category');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getComponents(): ComponentItemList
    {
        return new ComponentItemList(
            $this->cache->get('twig_doc.parsed.components'.$this->configReadTime, function () {
                $components = [];
                foreach ($this->componentsConfig as $componentData) {
                    try {
                        $components[] = $this->itemFactory->create($componentData);
                    } catch (InvalidComponentConfigurationException) {
                        continue;
                    }
                }

                return $components;
            })
        );
    }

    public function filter(string $filterQuery, string $filterType): ComponentItemList
    {
        $hash = sprintf('twig_doc_bundle.search.%s.%s', md5($filterQuery.$filterType), $this->configReadTime);

        return $this->cache->get($hash, function () use ($filterQuery, $filterType) {
            return $this->getComponents()->filter($filterQuery, $filterType);
        });
    }

    /**
     * @return ComponentInvalid[]
     *
     * @throws InvalidArgumentException
     */
    public function getInvalidComponents(): array
    {
        return $this->cache->get('twig_doc_bundle.invalid_components'.$this->configReadTime, function () {
            $invalid = array_filter($this->componentsConfig, function ($cmpData) {
                foreach ($this->getComponents()->getArrayCopy() as $cmp) {
                    if ($cmp->getName() === $cmpData['name'] ?? null) {
                        return false;
                    }
                }

                return true;
            });
            $invalidComponents = [];

            foreach ($invalid as $cmpData) {
                try {
                    $this->itemFactory->create($cmpData);
                } catch (InvalidComponentConfigurationException $e) {
                    $invalidComponents[] = new ComponentInvalid($e->getViolationList(), $cmpData);
                }
            }

            return $invalidComponents;
        });
    }

    public function getComponent(string $name): ?ComponentItem
    {
        return array_values(array_filter((array) $this->getComponents(), fn (ComponentItem $c) => $c->getName() === $name))[0] ?? null;
    }
}
