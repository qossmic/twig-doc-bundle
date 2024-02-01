<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Controller\TwigDocController;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Qossmic\TwigDocBundle\Twig\TwigDocExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(TwigDocController::class)]
#[UsesClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
#[CoversClass(ComponentService::class)]
#[UsesClass(TwigDocExtension::class)]
class TwigDocControllerTest extends WebTestCase
{
    public function testIndexReturnsStatus200(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        static::assertResponseIsSuccessful();
        static::assertCount(1, $crawler->filter('button.btn-primary'));
    }

    public function testFilterComponents(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/', ['filterQuery' => 'ButtonSubmit', 'filterType' => 'name']);

        $node = $crawler->filter('div.twig-doc-component');
        static::assertResponseIsSuccessful();
        static::assertCount(1, $node);
        static::assertEquals('Submit Button (ButtonSubmit.html.twig)', $node->filter('h3')->getNode(0)->nodeValue);
    }

    public function testInvalidComponentsRoute(): void
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/invalid');

        $node = $crawler->filter('div.error > h2');
        static::assertResponseIsSuccessful();
        static::assertEquals('InvalidComponent', $node->getNode(0)->nodeValue);
    }
}
