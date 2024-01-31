<?php

$config = [
    'categories' => [
        [
            'name' => 'MainCategory',
            'sub_categories' => [
                'SubCategory1',
                'SubCategory2',
            ]]
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
                    'link' => '#'
                ]
            ]
        ],

    ]
];

$container->loadFromExtension('twig_doc', $config);
