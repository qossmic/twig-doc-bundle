<?php

namespace Qossmic\TwigDocBundle\Configuration;

use Symfony\Component\Yaml\Yaml;

class YamlParser implements ParserInterface
{
    public function parse(string $data): array
    {
        $content = $this->fixIndentation($data);

        return Yaml::parse($content);
    }

    private function fixIndentation(string $content): string
    {
        $fileObject = new \SplFileObject('php://memory', 'r+');
        $fileObject->fwrite($content);
        $fileObject->rewind();

        $firstLineDetected = false;
        $indentationWhitespace = null;

        $lines = [];

        while ($fileObject->valid()) {
            $line = $fileObject->current();
            if (empty(trim($line))) {
                $fileObject->next();
                continue;
            }
            if ($firstLineDetected === false) {
                $firstLineDetected = true;
                # check for whitespaces at the beginning
                if (!preg_match('#^(\s+)#', $line, $matches)) {
                    # no leading whitespaces, indentation seems to be fine
                    return $content;
                }
                $indentationWhitespace = $matches[1];
            }
            $line = substr($line, strlen($indentationWhitespace));
            $lines[] = $line;
            $fileObject->next();
        }

        return implode("\n", $lines);
    }
}
