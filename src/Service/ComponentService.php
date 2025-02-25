<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Service;

use OpenSC\TwigDocBundle\Component\ComponentInvalid;
use OpenSC\TwigDocBundle\Component\ComponentItem;
use OpenSC\TwigDocBundle\Component\ComponentItemFactory;
use OpenSC\TwigDocBundle\Component\ComponentItemList;
use OpenSC\TwigDocBundle\Exception\InvalidComponentConfigurationException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

readonly class ComponentService
{
    public function __construct(
        private ComponentItemFactory $itemFactory,
        private array $componentsConfig,
        private CacheInterface $cache,
        private array $breakpointConfig,
        private int $configReadTime = 0
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

        return $this->cache->get($hash, fn () => $this->getComponents()->filter($filterQuery, $filterType));
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
        return array_values(array_filter((array) $this->getComponents(), static fn (ComponentItem $c) => $c->getName() === $name))[0] ?? null;
    }

    public function getBreakpoints(): array
    {
        return $this->breakpointConfig;
    }
}
