<?php



namespace Symfony\Component\HttpClient\Chunk;


class FirstChunk extends DataChunk
{
    
    public function isFirst(): bool
    {
        return true;
    }
}
