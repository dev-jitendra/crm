<?php

namespace ZBateson\MailMimeParser\Stream;

use ArrayIterator;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message\Part\ParentHeaderPart;
use ZBateson\MailMimeParser\Message\Part\MessagePart;


class HeaderStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    protected $part;

    
    public function __construct(MessagePart $part)
    {
        $this->part = $part;
    }

    
    private function getPartHeadersIterator()
    {
        if ($this->part instanceof ParentHeaderPart) {
            return $this->part->getRawHeaderIterator();
        } elseif ($this->part->getParent() !== null && $this->part->getParent()->isMime()) {
            return new ArrayIterator([
                [ 'Content-Type', $this->part->getContentType() ],
                [ 'Content-Disposition', $this->part->getContentDisposition() ],
                [ 'Content-Transfer-Encoding', $this->part->getContentTransferEncoding() ]
            ]);
        }
        return new ArrayIterator();
    }

    
    public function writePartHeadersTo(StreamInterface $stream)
    {
        foreach ($this->getPartHeadersIterator() as $header) {
            $stream->write("${header[0]}: ${header[1]}\r\n");
        }
        $stream->write("\r\n");
    }

    
    protected function createStream()
    {
        $stream = Psr7\stream_for();
        $this->writePartHeadersTo($stream);
        $stream->rewind();
        return $stream;
    }
}
