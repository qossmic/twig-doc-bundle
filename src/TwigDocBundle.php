<?php

declare(strict_types=1);

namespace Qossmic\TwigDocBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TwigDocBundle extends Bundle
{
    public function getPath(): string
    {
        return __DIR__.'/..';
    }
}
