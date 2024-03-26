<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use GuzzleHttp\Psr7\BufferStream;
use RuntimeException;


class Base64Stream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    private $buffer;

    
    private $remainder = '';

    
    private $position = 0;

    
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
        $this->buffer = new BufferStream();
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
        throw new RuntimeException('Cannot seek a Base64Stream');
    }

    
    public function isSeekable()
    {
        return false;
    }

    
    public function eof()
    {
        return ($this->buffer->eof() && $this->stream->eof());
    }

    
    private function fillBuffer($length)
    {
        $fill = 8192;
        while ($this->buffer->getSize() < $length) {
            $read = $this->stream->read($fill);
            if ($read === false || $read === '') {
                break;
            }
            $this->buffer->write(base64_decode($read));
        }
    }

    
    public function read($length)
    {
        
        if ($length <= 0 || $this->eof()) {
            return $this->stream->read($length);
        }
        $this->fillBuffer($length);
        $ret = $this->buffer->read($length);
        $this->position += strlen($ret);
        return $ret;
    }

    
    public function write($string)
    {
        $bytes = $this->remainder . $string;
        $len = strlen($bytes);
        if (($len % 3) !== 0) {
            $this->remainder = substr($bytes, -($len % 3));
            $bytes = substr($bytes, 0, $len - ($len % 3));
        } else {
            $this->remainder = '';
        }
        $this->stream->write(base64_encode($bytes));
        $written = strlen($string);
        $this->position += $len;
        return $written;
    }

    
    private function beforeClose()
    {
        if ($this->isWritable() && $this->remainder !== '') {
            $this->stream->write(base64_encode($this->remainder));
            $this->remainder = '';
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
