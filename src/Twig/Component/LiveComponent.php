<?php

namespace Qossmic\TwigDocBundle\Twig\Component;

use Qossmic\TwigDocBundle\Component\ComponentItem;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class LiveComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public array $params = [];

    #[LiveProp]
    public array $variation = [];

    #[LiveProp(hydrateWith: 'hydrateComponent', dehydrateWith: 'dehydrateComponent')]
    public ComponentItem $component;

    public function __construct(private readonly SerializerInterface $serializer)
    {
    }

    public function hydrateComponent(array $data): ComponentItem
    {
        return $this->serializer->denormalize($data, ComponentItem::class);
    }

    public function dehydrateComponent(ComponentItem $component): array
    {
        return $this->serializer->normalize($component);
    }

    #[LiveAction]
    public function apply(#[LiveArg] array $variation)
    {
        $this->variation = $variation;
    }
}
