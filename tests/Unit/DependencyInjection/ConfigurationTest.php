<?php

namespace Qossmic\TwigDocBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

#[CoversClass(Configuration::class)]
class ConfigurationTest extends TestCase
{
    #[DataProvider('getTestConfiguration')]
    public function testConfigTree(array $options, array $expectedResult)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, [$options]);

        $this->assertEquals($expectedResult, $config);
    }

    public static function getTestConfiguration(): iterable
    {
        yield 'Categories config' => [
            [
                'categories' => [
                    [
                        'name' => 'MainCategory',
                        'sub_categories' => [
                            'SubCategory1',
                            'SubCategory2',
                        ]
                    ]
                ],
            ],
            [
                'categories' => [
                    [
                        'name' => 'MainCategory',
                        'sub_categories' => [
                            'SubCategory1',
                            'SubCategory2',
                        ]
                    ]
                ],
                'components' => []
            ]
        ];

        yield 'Simple Component' => [
            [
                'categories' => [
                    [
                        'name' => 'MainCategory',
                        'sub_categories' => [
                            'SubCategory1',
                            'SubCategory2',
                        ]
                    ]
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
            ],
            [
                'categories' => [
                    [
                        'name' => 'MainCategory',
                        'sub_categories' => [
                            'SubCategory1',
                            'SubCategory2',
                        ]
                    ]
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
            ],
        ];
    }
}