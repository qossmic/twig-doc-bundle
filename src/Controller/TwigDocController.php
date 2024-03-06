<?php
declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Twig\Environment;
use Qossmic\TwigDocBundle\Service\ComponentService;

class TwigDocController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ComponentService $componentService,
        private readonly ?Profiler $profiler = null
    )
    {
    }

    public function index(Request $request): Response
    {
        $components = $this->componentService->getCategories();

        if ($filterQuery = $request->query->get('filterQuery')) {
            $filterType = $request->query->get('filterType');
            $components = $this->componentService->filter($filterQuery, $filterType);
        }

        return new Response(
            $this->twig->render('@TwigDoc/documentation.html.twig', [
                'components' => $components,
                'filterQuery' => $filterQuery,
                'filterType' => $filterType ?? null,
            ])
        );
    }

    public function invalidComponents(): Response
    {
        return new Response(
            $this->twig->render('@TwigDoc/pages/invalid_components.html.twig')
        );
    }

    public function componentView(Request $request): Response
    {
        $name = $request->query->get('name');
        $component = $this->componentService->getComponent($name);
        if (!$component) {
            throw new NotFoundHttpException(sprintf('Component %s is unknown', $name));
        }
        // disable profiler to get rid of toolbar in dev
        $this->profiler?->disable();
        return new Response(
            $this->twig->render('@TwigDoc/component.html.twig', [
                'component' => $component,
                'componentData' => $request->query->all('data'),
                'quantity' => $request->query->get('quantity')
            ])
        );
    }
}
