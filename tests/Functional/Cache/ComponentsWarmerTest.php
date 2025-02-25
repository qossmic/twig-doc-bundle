<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Tests\Functional\Cache;

use OpenSC\TwigDocBundle\Cache\ComponentsWarmer;
use OpenSC\TwigDocBundle\Component\ComponentItemFactory;
use OpenSC\TwigDocBundle\Component\ComponentItemList;
use OpenSC\TwigDocBundle\Component\Data\Faker;
use OpenSC\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use OpenSC\TwigDocBundle\Component\Data\Generator\NullGenerator;
use OpenSC\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use OpenSC\TwigDocBundle\Service\CategoryService;
use OpenSC\TwigDocBundle\Service\ComponentService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Psr\Container\ContainerInterface;
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
