<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;


class SeekingLimitStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    private $offset;

    
    private $limit;

    
    private $position = 0;

    
    public function __construct(
        StreamInterface $stream,
        $limit = -1,
        $offset = 0
    ) {
        $this->stream = $stream;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }

    
    public function tell()
    {
        return $this->position;
    }

    
    public function getSize()
    {
        $size = $this->stream->getSize();
        if ($size === null) {
            
            $pos = $this->stream->tell();
            $this->stream->seek(0, SEEK_END);
            $size = $this->stream->tell();
            $this->stream->seek($pos);
        }
        if ($this->limit === -1) {
            return $size - $this->offset;
        } else {
            return min([$this->limit, $size - $this->offset]);
        }
    }

    
    public function eof()
    {
        $size = $this->limit;
        if ($size === -1) {
            $size = $this->getSize();
        }
        return ($this->position >= $size);
    }

    
    private function doSeek($pos)
    {
        if ($this->limit !== -1) {
            $pos = min([$pos, $this->limit]);
        }
        $this->position = max([0, $pos]);
    }

    
    public function seek($offset, $whence = SEEK_SET)
    {
        $pos = $offset;
        switch ($whence) {
            case SEEK_CUR:
                $pos = $this->position + $offset;
                break;
            case SEEK_END:
                $pos = $this->limit + $offset;
                break;
            default:
                break;
        }
        $this->doSeek($pos);
    }

    
    public function setOffset($offset)
    {
        $this->offset = $offset;
        $this->position = 0;
    }

    
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    
    public function seekAndRead($length)
    {
        $this->stream->seek($this->offset + $this->position);
        if ($this->limit !== -1) {
            $length = min($length, $this->limit - $this->position);
            if ($length <= 0) {
                return '';
            }
        }
        return $this->stream->read($length);
    }

    
    public function read($length)
    {
        $pos = $this->stream->tell();
        $ret = $this->seekAndRead($length);
        $this->position += strlen($ret);
        $this->stream->seek($pos);
        if ($this->limit !== -1 && $this->position > $this->limit) {
            $ret = substr($ret, 0, -($this->position - $this->limit));
            $this->position = $this->limit;
        }
        return $ret;
    }
}
