<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Tests\Functional\Twig;

use OpenSC\TwigDocBundle\Component\ComponentItemFactory;
use OpenSC\TwigDocBundle\Component\ComponentItemList;
use OpenSC\TwigDocBundle\Component\Data\Faker;
use OpenSC\TwigDocBundle\Component\Data\Generator\FixtureGenerator;
use OpenSC\TwigDocBundle\Component\Data\Generator\NullGenerator;
use OpenSC\TwigDocBundle\Component\Data\Generator\ScalarGenerator;
use OpenSC\TwigDocBundle\Service\CategoryService;
use OpenSC\TwigDocBundle\Service\ComponentService;
use OpenSC\TwigDocBundle\Twig\TwigDocExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
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
