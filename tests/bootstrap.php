<?php

declare(strict_types=1);

use Qossmic\TwigDocBundle\Tests\TestApp\Kernel;
use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__).'/vendor/autoload.php';

$fileSystem = new Filesystem();

// build cache to avoid risky tests due to uncovered files from dependencyInjection
$kernel = new Kernel();
$fileSystem->remove($kernel->getCacheDir());
$kernel->boot();
$kernel->shutdown();
