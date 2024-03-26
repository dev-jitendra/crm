<?php

namespace ZBateson\MailMimeParser\Stream;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\AppendStream;
use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\MailMimeParser;
use ZBateson\MailMimeParser\Message\Part\MessagePart;
use ZBateson\MailMimeParser\Message\Part\ParentHeaderPart;
use ZBateson\MailMimeParser\Stream\StreamFactory;


class MessagePartStream implements StreamInterface
{
    use StreamDecoratorTrait;

    
    protected $streamFactory;

    
    protected $part;

    
    public function __construct(StreamFactory $sdf, MessagePart $part)
    {
        $this->streamFactory = $sdf;
        $this->part = $part;
    }

    
    private function getCharsetDecoratorForStream(StreamInterface $stream)
    {
        $charset = $this->part->getCharset();
        if (!empty($charset)) {
            $stream = $this->streamFactory->newCharsetStream(
                $stream,
                $charset,
                MailMimeParser::DEFAULT_CHARSET
            );
        }
        return $stream;
    }
    
    
    private function getTransferEncodingDecoratorForStream(StreamInterface $stream)
    {
        $encoding = $this->part->getContentTransferEncoding();
        $decorator = null;
        switch ($encoding) {
            case 'quoted-printable':
                $decorator = $this->streamFactory->newQuotedPrintableStream($stream);
                break;
            case 'base64':
                $decorator = $this->streamFactory->newBase64Stream(
                    $this->streamFactory->newChunkSplitStream($stream));
                break;
            case 'x-uuencode':
                $decorator = $this->streamFactory->newUUStream($stream);
                $decorator->setFilename($this->part->getFilename());
                break;
            default:
                return $stream;
        }
        return $decorator;
    }

    
    private function writePartContentTo(StreamInterface $stream)
    {
        $contentStream = $this->part->getContentStream();
        if ($contentStream !== null) {
            $copyStream = $this->streamFactory->newNonClosingStream($stream);
            $es = $this->getTransferEncodingDecoratorForStream($copyStream);
            $cs = $this->getCharsetDecoratorForStream($es);
            Psr7\copy_to_stream($contentStream, $cs);
            $cs->close();
        }
    }

    
    protected function getBoundaryAndChildStreams(ParentHeaderPart $part)
    {
        $boundary = $part->getHeaderParameter('Content-Type', 'boundary');
        if ($boundary === null) {
            return array_map(
                function ($child) {
                    return $child->getStream();
                },
                $part->getChildParts()
            );
        }
        $streams = [];
        foreach ($part->getChildParts() as $i => $child) {
            if ($i !== 0 || $part->hasContent()) {
                $streams[] = Psr7\stream_for("\r\n");
            }
            $streams[] = Psr7\stream_for("--$boundary\r\n");
            $streams[] = $child->getStream();
        }
        $streams[] = Psr7\stream_for("\r\n--$boundary--\r\n");
        
        return $streams;
    }

    
    protected function getStreamsArray()
    {
        $content = Psr7\stream_for();
        $this->writePartContentTo($content);
        $content->rewind();
        $streams = [ $this->streamFactory->newHeaderStream($this->part), $content ];

        
        $part = $this->part;
        if ($part instanceof ParentHeaderPart && $part->getChildCount()) {
            $streams = array_merge($streams, $this->getBoundaryAndChildStreams($part));
        }

        return $streams;
    }

    
    protected function createStream()
    {
        return new AppendStream($this->getStreamsArray());
    }
}
