<?php

namespace ZBateson\StreamDecorators;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use RuntimeException;


class QuotedPrintableStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    private $position = 0;

    
    private $lastLine = '';

    
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
        throw new RuntimeException('Cannot seek a QuotedPrintableStream');
    }

    
    public function isSeekable()
    {
        return false;
    }

    
    private function readEncodedChars($length, $pre = '')
    {
        $str = $pre . $this->stream->read($length);
        $len = strlen($str);
        if ($len > 0 && !preg_match('/^[0-9a-f]{2}$|^[\r\n]{1,2}.?$/is', $str) && $this->stream->isSeekable()) {
            $this->stream->seek(-$len, SEEK_CUR);
            return '3D';    
        }
        return $str;
    }

    
    private function decodeBlock($block)
    {
        if (substr($block, -1) === '=') {
            $block .= $this->readEncodedChars(2);
        } elseif (substr($block, -2, 1) === '=') {
            $first = substr($block, -1);
            $block = substr($block, 0, -1);
            $block .= $this->readEncodedChars(1, $first);
        }
        return quoted_printable_decode($block);
    }

    
    private function readRawDecodeAndAppend($length, &$str)
    {
        $block = $this->stream->read($length);
        if ($block === false || $block === '') {
            return -1;
        }
        $decoded = $this->decodeBlock($block);
        $count = strlen($decoded);
        $str .= $decoded;
        return $count;
    }

    
    public function read($length)
    {
        
        if ($length <= 0 || $this->eof()) {
            return $this->stream->read($length);
        }
        $count = 0;
        $bytes = '';
        while ($count < $length) {
            $nRead = $this->readRawDecodeAndAppend($length - $count, $bytes);
            if ($nRead === -1) {
                break;
            }
            $this->position += $nRead;
            $count += $nRead;
        }
        return $bytes;
    }

    
    public function write($string)
    {
        $encodedLine = quoted_printable_encode($this->lastLine);
        $lineAndString = rtrim(quoted_printable_encode($this->lastLine . $string), "\r\n");
        $write = substr($lineAndString, strlen($encodedLine));
        $this->stream->write($write);
        $written = strlen($string);
        $this->position += $written;

        $lpos = strrpos($lineAndString, "\n");
        $lastLine = $lineAndString;
        if ($lpos !== false) {
            $lastLine = substr($lineAndString, $lpos + 1);
        }
        $this->lastLine = quoted_printable_decode($lastLine);
        return $written;
    }

    
    private function beforeClose()
    {
        if ($this->isWritable() && $this->lastLine !== '') {
            $this->stream->write("\r\n");
            $this->lastLine = '';
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
