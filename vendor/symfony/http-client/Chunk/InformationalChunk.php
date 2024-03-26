<?php



namespace Symfony\Component\HttpClient\Chunk;


class InformationalChunk extends DataChunk
{
    private $status;

    public function __construct(int $statusCode, array $headers)
    {
        $this->status = [$statusCode, $headers];
    }

    
    public function getInformationalStatus(): ?array
    {
        return $this->status;
    }
}
