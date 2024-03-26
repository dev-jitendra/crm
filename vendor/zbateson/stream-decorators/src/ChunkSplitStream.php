<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;


class ChunkSplitStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    private $position;

    
    private $lineLength;

    
    private $lineEnding;

    
    private $lineEndingLength;

    
    public function __construct(StreamInterface $stream, $lineLength = 76, $lineEnding = "\r\n")
    {
        $this->stream = $stream;
        $this->lineLength = $lineLength;
        $this->lineEnding = $lineEnding;
        $this->lineEndingLength = strlen($this->lineEnding);
    }

    
    private function getChunkedString($string)
    {
        $firstLine = '';
        if ($this->tell() !== 0) {
            $next = $this->lineLength - ($this->position % ($this->lineLength + $this->lineEndingLength));
            if (strlen($string) > $next) {
                $firstLine = substr($string, 0, $next) . $this->lineEnding;
                $string = substr($string, $next);
            }
        }
        
        $chunked = $firstLine . chunk_split($string, $this->lineLength, $this->lineEnding);
        return substr($chunked, 0, strlen($chunked) - $this->lineEndingLength);
    }

    
    public function write($string)
    {
        $chunked = $this->getChunkedString($string);
        $this->position += strlen($chunked);
        return $this->stream->write($chunked);
    }

    
    private function beforeClose()
    {
        if ($this->position !== 0) {
            $this->stream->write($this->lineEnding);
        }
    }

    
    public function close()
    {
        $this->beforeClose();
        $this->stream->close();
    }

    
    public function detach()
    {
        $this->beforeClose();
        $this->stream->detach();
    }
}
