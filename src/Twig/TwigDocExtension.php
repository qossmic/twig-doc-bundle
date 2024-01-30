<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Twig;

use InvalidArgumentException;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Symfony\UX\TwigComponent\ComponentRendererInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;

class TwigDocExtension extends AbstractExtension
{
    public function __construct(
        private readonly ?ComponentRendererInterface $componentRenderer,
        private readonly ComponentService $componentService,
        private readonly Environment $twig
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderComponent', [$this, 'renderComponent']),
            new TwigFunction('filterComponents', [$this, 'filterComponents']),
        ];
    }

    public function filterComponents(string $filterQuery, string $type = null): array
    {
        return $this->componentService->filter($filterQuery, $type);
    }

    public function renderComponent(ComponentItem $item, array $params): string
    {
        if ($item->getMainCategory()->getName() === CategoryService::INVALID_CATEGORY) {
            return $this->twig->render('@TwigDoc/error/invalid_component.html.twig', ['component' => $item]);
        }

        if ($this->componentRenderer === null) {
            return $this->renderFallback($item, $params);
        }

        try {
            return $this->componentRenderer->createAndRender($item->getName(), $params);
        } catch (InvalidArgumentException $e) {
            # no ux-component found, so try to render as normal template
            return $this->renderFallback($item, $params);
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    private function renderFallback(ComponentItem $item, array $params): string
    {
        return $this->twig->render($item->getName().'.html.twig', $params);
    }
}
