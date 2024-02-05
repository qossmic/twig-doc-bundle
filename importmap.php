<?php

return [
    '@qossmic/twig-doc-bundle' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => '@symfony/stimulus-bundle/loader.js',
    ],
    '@hotwired/stimulus' => [
        'url' => 'https://ga.jspm.io/npm:@hotwired/stimulus@3.2.1/dist/stimulus.js',
    ],
    '@symfony/ux-live-component' => [
        'path' => './vendor/symfony/ux-live-component/assets/dist/live_controller.js',
    ],
];
