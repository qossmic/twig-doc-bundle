<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Twig;

use InvalidArgumentException;
use Qossmic\TwigDocBundle\Component\ComponentCategory;
use Qossmic\TwigDocBundle\Component\ComponentInvalid;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Symfony\UX\TwigComponent\ComponentRendererInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Qossmic\TwigDocBundle\Service\ComponentService;

class TwigDocExtension extends AbstractExtension
{
    public function __construct(
        private readonly ComponentRendererInterface $componentRenderer,
        private readonly ComponentService $componentService,
        private readonly CategoryService $categoryService,
        private readonly Environment $twig
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderComponent', [$this, 'renderComponent']),
            new TwigFunction('filterComponents', [$this, 'filterComponents']),
            new TwigFunction('getInvalidComponents', [$this, 'getInvalidComponents']),
            new TwigFunction('getSubCategories', [$this, 'getSubCategories'])
        ];
    }

    public function filterComponents(string $filterQuery, string $type = null): array
    {
        return $this->componentService->filter($filterQuery, $type);
    }

    public function renderComponent(ComponentItem $item, array $params): string
    {
        try {
            # add key-prop to re-calculate a new id to force re-rendering of component
            # see Symfony\UX\LiveComponent\Util\LiveControllerAttributesCreator::KEY_PROP_NAME
            if (!isset($params['key'])) {
                $params['key'] = mt_rand(0, 100000);
            }
            return $this->componentRenderer->createAndRender($item->getName(), $params);
        } catch (InvalidArgumentException $e) {
            # no ux-component found, so try to render as normal template
            return $this->renderFallback($item, $params);
        }
    }

    /**
     * @return ComponentInvalid[]
     */
    public function getInvalidComponents(): array
    {
        return $this->componentService->getInvalidComponents();
    }

    /**
     * @return ComponentCategory[]
     */
    public function getSubCategories(string $mainCategoryName = null): array
    {
        return $this->categoryService->getSubCategories($this->categoryService->getCategory($mainCategoryName));
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
