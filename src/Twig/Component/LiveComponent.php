<?php

namespace Qossmic\TwigDocBundle\Twig\Component;

use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
class LiveComponent
{
    use DefaultActionTrait;

    #[LiveProp(useSerializerForHydration: true)]
    public ComponentItem $component;

    #[LiveProp]
    public string $breakpoint = ComponentService::BREAKPOINT_S;

    #[LiveProp(writable: true)]
    public array $componentData = [];

    #[LiveProp(writable: true)]
    public int $quantity = 1;

    public readonly array $breakpoints;

    public function __construct(
        private readonly ComponentService $componentService
    )
    {
        $this->breakpoints = $this->componentService->getBreakpoints();
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->componentData = array_values($this->component->getVariations())[0] ?? [];
    }

    #[LiveAction]
    public function apply(#[LiveArg] string $breakpoint): void
    {
        if (!isset($this->breakpoints[$breakpoint])) {
            throw new \InvalidArgumentException(sprintf('Unknown breakpoint: %s', $breakpoint));
        }

        $this->breakpoint = $breakpoint;
    }

    #[LiveAction]
    public function showMore(): void
    {
        $this->quantity++;
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->componentData = array_values($this->component->getVariations())[0] ?? [];
        $this->quantity = 1;
    }
}
