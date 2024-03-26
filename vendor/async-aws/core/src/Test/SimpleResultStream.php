<?php

declare(strict_types=1);

namespace AsyncAws\Core\Test;

use AsyncAws\Core\Stream\ResultStream;


class SimpleResultStream implements ResultStream
{
    
    private $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function getChunks(): iterable
    {
        yield $this->data;
    }

    public function getContentAsString(): string
    {
        return $this->data;
    }

    public function getContentAsResource()
    {
        $resource = fopen('php:

        try {
            fwrite($resource, $this->data);

            
            fseek($resource, 0, \SEEK_SET);

            return $resource;
        } catch (\Throwable $e) {
            fclose($resource);

            throw $e;
        }
    }
}
