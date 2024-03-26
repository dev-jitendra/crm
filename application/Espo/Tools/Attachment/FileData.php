<?php


namespace Espo\Tools\Attachment;

use Psr\Http\Message\StreamInterface;


class FileData
{
    private ?string $name;
    private ?string $type;
    private StreamInterface $stream;
    private int $size;

    public function __construct(
        ?string $name,
        ?string $type,
        StreamInterface $stream,
        int $size
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->stream = $stream;
        $this->size = $size;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getStream(): StreamInterface
    {
        return $this->stream;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
