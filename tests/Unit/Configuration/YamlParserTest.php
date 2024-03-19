<?php

namespace Qossmic\TwigDocBundle\Tests\Unit\Configuration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Qossmic\TwigDocBundle\Configuration\YamlParser;
use Symfony\Component\Yaml\Exception\ParseException;

#[CoversClass(YamlParser::class)]
class YamlParserTest extends TestCase
{
    public function testParseThrowsExceptionForInvalidYaml(): void
    {
        static::expectException(ParseException::class);
        $yaml = "  key: \nyaml ain't markup language";

        $parser = new YamlParser();

        $parser->parse($yaml);
    }

    #[DataProvider('getIndentationTestCases')]
    public function testParseFixesIndentation(string $yaml, array $expected): void
    {
        $parser = new YamlParser();

        $result = $parser->parse($yaml);

        static::assertEquals($expected, $result);
    }

    public static function getIndentationTestCases(): iterable
    {
        yield 'simple key-value test' => [
            'yaml' => "        key: yaml ain't markup language",
            'expected' => ['key' => "yaml ain't markup language"],
        ];

        yield 'indentationFix' => [
            'yaml' => <<<TEXT
                    key: value
                    otherKey:
                        sub: subValue
TEXT,
            [
                'key' => 'value',
                'otherKey' => [
                    'sub' => 'subValue',
                ],
            ],
        ];

        yield 'correctly formatted yaml' => [
            'yaml' => <<<TEXT

key: value
otherKey:
  sub: subValue

TEXT,
            [
                'key' => 'value',
                'otherKey' => [
                    'sub' => 'subValue',
                ],
            ],
        ];
    }
}
