<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle\Tests\Functional\Twig;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Component\ComponentItemList;
use Qossmic\TwigDocBundle\Component\Data\Faker;
use Qossmic\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\NullGenerator;
use Qossmic\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Qossmic\TwigDocBundle\Twig\TwigDocExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\TwigFunction;

#[CoversClass(TwigDocExtension::class)]
#[UsesClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
#[UsesClass(ComponentService::class)]
#[UsesClass(ComponentItemList::class)]
#[UsesClass(Faker::class)]
#[UsesClass(ScalarGenerator::class)]
#[UsesClass(FixtureGenerator::class)]
#[UsesClass(NullGenerator::class)]
class TwigDocExtensionTest extends KernelTestCase
{
    public function testGetFunctions(): void
    {
        $extension = static::getContainer()->get(TwigDocExtension::class);
        $functions = $extension->getFunctions();

        static::assertCount(5, $functions);

        foreach ($functions as $function) {
            static::assertInstanceOf(TwigFunction::class, $function);
        }
    }

    public function testRenderComponentUsesFallbackWhenUXComponentsMissing(): void
    {
        $componentService = static::getContainer()->get(ComponentService::class);
        $extension = static::getContainer()->get(TwigDocExtension::class);

        $result = $extension->renderComponent(
            $componentService->getComponent('Button'), ['type' => 'primary', 'text' => 'some text']
        );

        static::assertIsString($result);
    }
}
