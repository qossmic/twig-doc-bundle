<?php

declare(strict_types=1);

namespace OpenSC\TwigDocBundle\Tests\TestApp\Component\Data\Generator;

use OpenSC\TwigDocBundle\Component\Data\GeneratorInterface;
use OpenSC\TwigDocBundle\Tests\TestApp\Entity\Special;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('twig_doc.data_generator', ['priority' => 10])]
class CustomGenerator implements GeneratorInterface
{
    public function supports(string $type, mixed $context = null): bool
    {
        return $type === Special::class;
    }

    public function generate(string $type, mixed $context = null): Special
    {
        return new Special([
            'key' => 'value',
        ]);
    }
}
