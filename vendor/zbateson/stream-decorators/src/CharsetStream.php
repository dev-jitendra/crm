<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use ZBateson\MbWrapper\MbWrapper;
use RuntimeException;


class CharsetStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    protected $converter = null;
    
    
    protected $streamCharset = 'ISO-8859-1';
    
    
    protected $stringCharset = 'UTF-8';

    
    private $position = 0;

    
    private $bufferLength = 0;

    
    private $buffer = '';

    
    public function __construct(StreamInterface $stream, $streamCharset = 'ISO-8859-1', $stringCharset = 'UTF-8')
    {
        $this->stream = $stream;
        $this->converter = new MbWrapper();
        $this->streamCharset = $streamCharset;
        $this->stringCharset = $stringCharset;
    }

    
    public function tell()
    {
        return $this->position;
    }

    
    public function getSize()
    {
        return null;
    }

    
    public function seek($offset, $whence = SEEK_SET)
    {
        throw new RuntimeException('Cannot seek a CharsetStream');
    }

    
    public function isSeekable()
    {
        return false;
    }

    
    private function readRawCharsIntoBuffer($length)
    {
        $n = ceil(($length + 32) / 4.0) * 4;
        while ($this->bufferLength < $n) {
            $raw = $this->stream->read($n + 512);
            if ($raw === false || $raw === '') {
                return;
            }
            $this->buffer .= $raw;
            $this->bufferLength = $this->converter->getLength($this->buffer, $this->streamCharset);
        }
    }

    
    public function eof()
    {
        return ($this->bufferLength === 0 && $this->stream->eof());
    }

    
    public function read($length)
    {
        
        if ($length <= 0 || $this->eof()) {
            return $this->stream->read($length);
        }
        $this->readRawCharsIntoBuffer($length);
        $numChars = min([$this->bufferLength, $length]);
        $chars = $this->converter->getSubstr($this->buffer, $this->streamCharset, 0, $numChars);
        
        $this->position += $numChars;
        $this->buffer = $this->converter->getSubstr($this->buffer, $this->streamCharset, $numChars);
        $this->bufferLength = $this->bufferLength - $numChars;

        return $this->converter->convert($chars, $this->streamCharset, $this->stringCharset);
    }

    
    public function write($string)
    {
        $converted = $this->converter->convert($string, $this->stringCharset, $this->streamCharset);
        $written = $this->converter->getLength($converted, $this->streamCharset);
        $this->position += $written;
        return $this->stream->write($converted);
    }
}
