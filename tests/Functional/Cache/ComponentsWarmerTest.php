<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Functional\Cache;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Container\ContainerInterface;
use Qossmic\TwigDocBundle\Cache\ComponentsWarmer;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Component\ComponentItemList;
use Qossmic\TwigDocBundle\Component\Data\Faker;
use Qossmic\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\NullGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(ComponentsWarmer::class)]
#[UsesClass(ComponentItemFactory::class)]
#[UsesClass(ComponentService::class)]
#[UsesClass(CategoryService::class)]
#[UsesClass(ComponentItemList::class)]
#[UsesClass(Faker::class)]
#[UsesClass(ScalarGenerator::class)]
#[UsesClass(FixtureGenerator::class)]
#[UsesClass(NullGenerator::class)]
class ComponentsWarmerTest extends KernelTestCase
{
    public function testWarmUp(): void
    {
        $service = static::getContainer()->get(ComponentService::class);
        $warmer = static::getContainer()->get('twig_doc.cache_warmer');

        $warmer->warmUp('');

        $time = microtime(true);

        $service->getComponents();

        static::assertLessThan(.1, microtime(true) - $time);
    }

    public function testIsOptional(): void
    {
        $warmer = new ComponentsWarmer($this->createMock(ContainerInterface::class));

        static::assertTrue($warmer->isOptional());
    }
}
