<?php

namespace Qossmic\TwigDocBundle\Tests\Functional\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Qossmic\TwigDocBundle\Component\ComponentInvalid;
use Qossmic\TwigDocBundle\Component\ComponentItem;
use Qossmic\TwigDocBundle\Component\ComponentItemFactory;
use Qossmic\TwigDocBundle\Service\CategoryService;
use Qossmic\TwigDocBundle\Service\ComponentService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(ComponentService::class)]
#[UsesClass(ComponentItemFactory::class)]
#[UsesClass(CategoryService::class)]
class ComponentServiceTest extends KernelTestCase
{
    #[DataProvider('getFilterTestCases')]
    public function testFilter(string $query, string $type, array $expectedCounts): void
    {
        $service = static::getContainer()->get(ComponentService::class);

        $components = $service->filter($query, $type);

        foreach ($expectedCounts as $category => $count) {
            static::assertCount($count, $components[$category]);
        }
    }

    public function testGetComponent(): void
    {
        $service = static::getContainer()->get(ComponentService::class);

        $component = $service->getComponent('ButtonAction');

        static::assertInstanceOf(ComponentItem::class, $component);
        static::assertEquals('ButtonAction', $component->getName());
    }

    public function testGetComponentsByCategory(): void
    {
        $service = static::getContainer()->get(ComponentService::class);

        $result = $service->getComponentsByCategory('MainCategory');

        static::assertCount(4, $result);
    }

    public function testGetCategories()
    {
        $service = static::getContainer()->get(ComponentService::class);

        $categories = $service->getCategories();

        static::assertCount(1, $categories);
    }

    public function testGetInvalidComponents()
    {
        $service = static::getContainer()->get(ComponentService::class);

        $invalid = $service->getInvalidComponents();

        static::assertCount(1, $invalid);
        foreach ($invalid as $component) {
            static::assertInstanceOf(ComponentInvalid::class, $component);
        }
    }

    public function testParsePerformance(): void
    {
        $factory = static::getContainer()->get(ComponentItemFactory::class);

        $start = microtime(true);

        new ComponentService($factory, $this->getLargeConfig());

        $elapsedTime = microtime(true) - $start;

        static::assertLessThan(1.5, $elapsedTime);
    }

    public static function getFilterTestCases(): iterable
    {
        yield 'name' => [
            'query' => 'button',
            'type' => 'name',
            'expectedCounts' => [
                'MainCategory' => 3
            ]
        ];

        yield 'category' => [
            'query' => 'MainCategory',
            'type' => 'category',
            'expectedCounts' => [
                'MainCategory' => 4
            ]
        ];

        yield 'sub_category' => [
            'query' => 'SubCategory2',
            'type' => 'sub_category',
            'expectedCounts' => [
                'MainCategory' => 1
            ]
        ];

        yield 'tags' => [
            'query' => 'snippet',
            'type' => 'tags',
            'expectedCounts' => [
                'MainCategory' => 1
            ]
        ];

        yield 'any' => [
            'query' => 'action',
            'type' => '',
            'expectedCounts' => [
                'MainCategory' => 1
            ]
        ];
    }

    private function getLargeConfig(): array
    {
        return array_fill(0, 5000, [
            'name' => 'component',
            'title' => 'title',
            'description' => 'long long text long long text long long text long long text long long text long long text long long text ',
            'category' => 'MainCategory',
            'path' => 'path/ultra/long/sub/dir/bla/blub/blub/blub/component.html.twig',
            'renderPath' => 'component.html.twig',
            'tags' => [
                'tag1',
                'tag2',
                'tag3',
                'tag4',
                'tag5',
                'tag6',
                'tag7',
                'tag8',
                'tag9',
            ],
            'parameters' => [
                'param1' => 'String',
                'param2' => 'String',
                'param3' => 'String',
                'param4' => 'String',
                'param5' => [
                    'param1' => 'String',
                    'param2' => 'String',
                    'param3' => 'String',
                    'param4' => 'String',
                ],
                'param6' => [
                    'param1' => 'String',
                    'param2' => 'String',
                    'param3' => 'String',
                    'param4' => 'String',
                ]
            ],
            'variations' => [
                'variation1' => [
                    'param1' => 'String',
                    'param2' => 'String',
                    'param3' => 'String',
                    'param4' => 'String',
                    'param5' => [
                        'param1' => 'String',
                        'param2' => 'String',
                        'param3' => 'String',
                        'param4' => 'String',
                    ],
                    'param6' => [
                        'param1' => 'String',
                        'param2' => 'String',
                        'param3' => 'String',
                        'param4' => 'String',
                    ]
                ],
                'variation2' => [
                    'param1' => 'String',
                    'param2' => 'String',
                    'param3' => 'String',
                    'param4' => 'String',
                    'param5' => [
                        'param1' => 'String',
                        'param2' => 'String',
                        'param3' => 'String',
                        'param4' => 'String',
                    ],
                    'param6' => [
                        'param1' => 'String',
                        'param2' => 'String',
                        'param3' => 'String',
                        'param4' => 'String',
                    ]
                ],
                'variation3' => [
                    'param1' => 'String',
                    'param2' => 'String',
                    'param3' => 'String',
                    'param4' => 'String',
                    'param5' => [
                        'param1' => 'String',
                        'param2' => 'String',
                        'param3' => 'String',
                        'param4' => 'String',
                    ],
                    'param6' => [
                        'param1' => 'String',
                        'param2' => 'String',
                        'param3' => 'String',
                        'param4' => 'String',
                    ]
                ]
            ]
        ]);
    }
}
