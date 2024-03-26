<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use GuzzleHttp\Psr7\BufferStream;
use RuntimeException;


class PregReplaceFilterStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    private $pattern;

    
    private $replacement;

    
    private $buffer;

    public function __construct(StreamInterface $stream, $pattern, $replacement)
    {
        $this->stream = $stream;
        $this->pattern = $pattern;
        $this->replacement = $replacement;
        $this->buffer = new BufferStream();
    }

    
    public function eof()
    {
        return ($this->buffer->eof() && $this->stream->eof());
    }

    
    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException('Cannot seek a PregReplaceFilterStream');
    }

    
    public function isSeekable()
    {
        return false;
    }

    
    private function fillBuffer($length)
    {
        $fill = intval(max([$length, 8192]));
        while ($this->buffer->getSize() < $length) {
            $read = $this->stream->read($fill);
            if ($read === false || $read === '') {
                break;
            }
            $this->buffer->write(preg_replace($this->pattern, $this->replacement, $read));
        }
    }

    
    public function read($length)
    {
        $this->fillBuffer($length);
        return $this->buffer->read($length);
    }
}
