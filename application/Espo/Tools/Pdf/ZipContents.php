<?php


namespace Espo\Tools\Pdf;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Stream;

use RuntimeException;

class ZipContents implements Contents
{
    public function __construct(private string $filePath) {}

    public function getStream(): StreamInterface
    {
        $resource = fopen($this->filePath, 'r+');

        if ($resource === false) {
            throw new RuntimeException("Could not open {$this->filePath}.");
        }

        return new Stream($resource);
    }

    public function getString(): string
    {
        return $this->getStream()->getContents();
    }

    public function getLength(): int
    {
        return (int) $this->getStream()->getSize();
    }
}
