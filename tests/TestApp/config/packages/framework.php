<?php

declare(strict_types=1);

$configuration = [
    'secret' => 'F00',
    'session' => [
        'handler_id' => null,
        'storage_factory_id' => 'session.storage.factory.mock_file',
    ],
    'test' => true,
];

$container->loadFromExtension('framework', $configuration);
