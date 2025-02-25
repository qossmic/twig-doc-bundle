<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Tests\Functional\Controller;

use OpenSC\TwigDocBundle\Component\ComponentItemFactory;
use OpenSC\TwigDocBundle\Component\ComponentItemList;
use OpenSC\TwigDocBundle\Component\Data\Faker;
use OpenSC\TwigDocBundle\Controller\TwigDocController;
use OpenSC\TwigDocBundle\Service\CategoryService;
use OpenSC\TwigDocBundle\Service\ComponentService;
use OpenSC\TwigDocBundle\Twig\TwigDocExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
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
