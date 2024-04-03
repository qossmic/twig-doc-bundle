<?php

declare(strict_types=1);

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
    public function testConfigTree(array $options, array $expectedResult): void
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
                'directories' => [
                    __DIR__.'/../../TestApp/templates/components',
                    __DIR__.'/../../TestApp/templates/snippets',
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
            ],
            [
                'doc_identifier' => 'TWIG_DOC',
                'breakpoints' => [
                    'small' => 240,
                    'medium' => 640,
                    'large' => 768,
                ],
                'directories' => [
                    __DIR__.'/../../TestApp/templates/components',
                    __DIR__.'/../../TestApp/templates/snippets',
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
                'components' => [],
            ],
        ];

        yield 'Simple Component' => [
            [
                'directories' => [
                    __DIR__.'/../../TestApp/templates/components',
                    __DIR__.'/../../TestApp/templates/snippets',
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
                ],
            ],
            [
                'doc_identifier' => 'TWIG_DOC',
                'breakpoints' => [
                    'small' => 240,
                    'medium' => 640,
                    'large' => 768,
                ],
                'directories' => [
                    __DIR__.'/../../TestApp/templates/components',
                    __DIR__.'/../../TestApp/templates/snippets',
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
                ],
            ],
        ];

        yield 'Breakpoint config' => [
            [
                'breakpoints' => [
                    'iphone' => 568,
                    'galaxy s10' => 658,
                    'generic' => 896,
                ],
            ],
            [
                'breakpoints' => [
                    'iphone' => 568,
                    'galaxy s10' => 658,
                    'generic' => 896,
                ],
                'doc_identifier' => 'TWIG_DOC',
                'directories' => [
                    '%twig.default_path%/components',
                ],
                'categories' => [],
                'components' => [],
            ],
        ];
    }
}
