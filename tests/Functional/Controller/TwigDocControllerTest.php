<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Component\ComponentItemList;
use Qossmic\TwigDocBundle\Component\Data\Faker;
use Qossmic\TwigDocBundle\Controller\TwigDocController;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Qossmic\TwigDocBundle\Twig\TwigDocExtension;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(TwigDocController::class)]
#[UsesClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
#[CoversClass(ComponentService::class)]
#[UsesClass(TwigDocExtension::class)]
#[UsesClass(ComponentItemList::class)]
#[UsesClass(Faker::class)]
class TwigDocControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function testIndexReturnsStatus200(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        static::assertResponseIsSuccessful();
        static::assertCount(1, $crawler->filter('button.btn-primary'));
    }

    public function testFilterComponents(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/', ['filterQuery' => 'ButtonSubmit', 'filterType' => 'name']);

        $node = $crawler->filter('div.twig-doc-component');
        $nodeContent = $node->filter('h3')->getNode(0)->nodeValue;
        static::assertResponseIsSuccessful();
        static::assertCount(1, $node);
        static::assertStringEndsWith('(tests/TestApp/templates/components/ButtonSubmit.html.twig)', $nodeContent);
        static::assertStringStartsWith('Submit Button', $nodeContent);
    }

    public function testInvalidComponentsRoute(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/invalid');

        $node = $crawler->filter('div.error > h2');
        static::assertResponseIsSuccessful();
        static::assertEquals('InvalidComponent', $node->getNode(0)->nodeValue);
    }

    public function testComponentViewRoute(): void
    {
        $crawler = $this->client->request(
            Request::METHOD_GET,
            '/component-view',
            [
                'quantity' => 1,
                'name' => 'Button',
                'data' => [
                    'type' => 'primary',
                    'text' => 'btn-text',
                ],
            ]
        );

        $node = $crawler->filter('button.btn-primary');
        static::assertResponseIsSuccessful();
        static::assertEquals('btn-text', $node->getNode(0)->nodeValue);
    }

    public function testComponentViewRouteReturns404(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            '/component-view',
            [
                'name' => 'notExistingComponent',
            ]
        );

        static::assertResponseStatusCodeSame(404);
    }
}
