<?php

namespace ZBateson\MailMimeParser\Stream;

use Psr\Http\Message\StreamInterface;
use ZBateson\StreamDecorators\Base64Stream;
use ZBateson\StreamDecorators\CharsetStream;
use ZBateson\StreamDecorators\ChunkSplitStream;
use ZBateson\StreamDecorators\SeekingLimitStream;
use ZBateson\StreamDecorators\NonClosingStream;
use ZBateson\StreamDecorators\PregReplaceFilterStream;
use ZBateson\StreamDecorators\QuotedPrintableStream;
use ZBateson\StreamDecorators\UUStream;
use ZBateson\MailMimeParser\Message\Part\MessagePart;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;


class StreamFactory
{
    
    public function getLimitedPartStream(StreamInterface $stream, PartBuilder $part)
    {
        return $this->newLimitStream(
            $stream,
            $part->getStreamPartLength(),
            $part->getStreamPartStartOffset()
        );
    }

    
    public function getLimitedContentStream(StreamInterface $stream, PartBuilder $part)
    {
        $length = $part->getStreamContentLength();
        if ($length !== 0) {
            return $this->newLimitStream(
                $stream,
                $part->getStreamContentLength(),
                $part->getStreamContentStartOffset()
            );
        }
        return null;
    }

    
    private function newLimitStream(StreamInterface $stream, $length, $start)
    {
        return new SeekingLimitStream(
            $this->newNonClosingStream($stream),
            $length,
            $start
        );
    }

    
    public function newNonClosingStream(StreamInterface $stream)
    {
        return new NonClosingStream($stream);
    }

    
    public function newChunkSplitStream(StreamInterface $stream)
    {
        return new ChunkSplitStream($stream);
    }

    
    public function newBase64Stream(StreamInterface $stream)
    {
        return new Base64Stream(
            new PregReplaceFilterStream($stream, '/[^a-zA-Z0-9\/\+=]/', '')
        );
    }

    
    public function newQuotedPrintableStream(StreamInterface $stream)
    {
        return new QuotedPrintableStream($stream);
    }

    
    public function newUUStream(StreamInterface $stream)
    {
        return new UUStream($stream);
    }

    
    public function newCharsetStream(StreamInterface $stream, $fromCharset, $toCharset)
    {
        return new CharsetStream($stream, $fromCharset, $toCharset);
    }

    
    public function newMessagePartStream(MessagePart $part)
    {
        return new MessagePartStream($this, $part);
    }

    
    public function newHeaderStream(MessagePart $part)
    {
        return new HeaderStream($part);
    }
}
