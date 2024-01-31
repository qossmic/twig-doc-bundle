<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\CoversNothing;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversNothing]
class TwigDocControllerTest extends WebTestCase
{
    public function testReturnsStatus200()
    {
        $client = self::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/');

        static::assertResponseIsSuccessful();
        static::assertCount(1, $crawler->filter('button.btn-primary'));
    }
}
