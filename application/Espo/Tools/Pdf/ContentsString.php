<?php


namespace Espo\Tools\Pdf;

use Psr\Http\Message\StreamInterface;

use GuzzleHttp\Psr7\Stream;

use RuntimeException;

class ContentsString implements Contents
{
    private string $contents;

    private function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function getStream(): StreamInterface
    {
        $resource = fopen('php:

        if ($resource === false) {
            throw new RuntimeException("Could not open temp.");
        }

        fwrite($resource, $this->getString());
        rewind($resource);

        return new Stream($resource);
    }

    public function getString(): string
    {
        return $this->contents;
    }

    public function getLength(): int
    {
        return strlen($this->contents);
    }

    public static function createFromString(string $contents): ContentsString
    {
        $obj = new self($contents);

        return $obj;
    }
}
