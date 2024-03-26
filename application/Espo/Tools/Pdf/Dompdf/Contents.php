<?php


namespace Espo\Tools\Pdf\Dompdf;

use Espo\Tools\Pdf\Contents as ContentsInterface;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Dompdf\Dompdf;

use RuntimeException;

class Contents implements ContentsInterface
{
    private ?string $string = null;

    public function __construct(private Dompdf $pdf) {}

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
        if ($this->string === null) {
            $this->string = $this->pdf->output();
        }

        return $this->string ?? '';
    }

    public function getLength(): int
    {
        return strlen($this->getString());
    }
}
