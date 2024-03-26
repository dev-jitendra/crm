<?php



namespace Symfony\Contracts\HttpClient;


interface ResponseStreamInterface extends \Iterator
{
    public function key(): ResponseInterface;

    public function current(): ChunkInterface;
}
