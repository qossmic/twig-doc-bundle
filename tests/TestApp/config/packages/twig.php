<?php

declare(strict_types=1);

$container->loadFromExtension('twig', [
    'default_path' => '%kernel.project_dir%/tests/TestApp/templates',
]);
