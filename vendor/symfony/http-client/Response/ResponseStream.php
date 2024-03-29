<?php



namespace Symfony\Component\HttpClient\Response;

use Symfony\Contracts\HttpClient\ChunkInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;


final class ResponseStream implements ResponseStreamInterface
{
    private $generator;

    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
    }

    public function key(): ResponseInterface
    {
        return $this->generator->key();
    }

    public function current(): ChunkInterface
    {
        return $this->generator->current();
    }

    public function next(): void
    {
        $this->generator->next();
    }

    public function rewind(): void
    {
        $this->generator->rewind();
    }

    public function valid(): bool
    {
        return $this->generator->valid();
    }
}
