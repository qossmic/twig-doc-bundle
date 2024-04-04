<?php

declare(strict_types=1);

$config = [
    'directories' => [
        '%twig.default_path%/snippets',
    ],
    'categories' => [
        [
            'name' => 'MainCategory',
            'sub_categories' => [
                'SubCategory1',
                'SubCategory2',
            ],
        ],
    ],
    'components' => [
        [
            'name' => 'ButtonAction',
            'title' => 'Action Button',
            'description' => 'Nice action button',
            'category' => 'MainCategory',
            'sub_category' => 'SubCategory1',
            'tags' => [
                'buttons',
            ],
            'parameters' => [
                'type' => 'String',
                'msg' => 'String',
                'link' => 'String',
            ],
            'variations' => [
                'default' => [
                    'type' => 'primary',
                    'msg' => 'Click Me',
                    'link' => '#',
                ],
            ],
        ],
        [
            'name' => 'ButtonSubmit',
            'title' => 'Submit Button',
            'description' => 'Nice submit button',
            'category' => 'MainCategory',
            'sub_category' => 'SubCategory1',
            'tags' => [
                'buttons',
            ],
            'parameters' => [
                'type' => 'String',
                'msg' => 'String',
                'link' => 'String',
            ],
            'variations' => [
                'default' => [
                    'type' => 'primary',
                    'msg' => 'Click Me',
                    'link' => '#',
                ],
            ],
        ],
        [
            'name' => 'InvalidComponent',
            'title' => 'Invalid Test Component',
            'description' => 'invalid config for testing purposes',
            'category' => 'InvalidCategory',
            'sub_category' => 'SubCategory1',
            'tags' => [],
            'parameters' => [],
            'variations' => [
                'default' => [],
            ],
        ],
    ],
];

$container->loadFromExtension('twig_doc', $config);
