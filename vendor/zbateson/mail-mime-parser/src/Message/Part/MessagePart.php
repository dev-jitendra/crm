<?php

namespace ZBateson\MailMimeParser\Message\Part;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\StreamWrapper;
use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\MailMimeParser;
use ZBateson\MailMimeParser\Stream\StreamFactory;


abstract class MessagePart
{
    
    protected $partStreamFilterManager;

    
    protected $streamFactory;

    
    protected $parent;

    
    protected $stream;

    
    protected $contentStream;

    
    protected $charsetOverride;

    
    protected $ignoreTransferEncoding;

    
    public function __construct(
        PartStreamFilterManager $partStreamFilterManager,
        StreamFactory $streamFactory,
        StreamInterface $stream = null,
        StreamInterface $contentStream = null
    ) {
        $this->partStreamFilterManager = $partStreamFilterManager;
        $this->streamFactory = $streamFactory;

        $this->stream = $stream;
        $this->contentStream = $contentStream;
        if ($contentStream !== null) {
            $partStreamFilterManager->setStream(
                $contentStream
            );
        }
    }

    
    public function __destruct()
    {
        if ($this->stream !== null) {
            $this->stream->close();
        }
        if ($this->contentStream !== null) {
            $this->contentStream->close();
        }
    }

    
    protected function onChange()
    {
        $this->markAsChanged();
        if ($this->parent !== null) {
            $this->parent->onChange();
        }
    }

    
    public function markAsChanged()
    {
        
        
        $this->stream = null;
    }

    
    public function hasContent()
    {
        return ($this->contentStream !== null);
    }

    
    public abstract function isTextPart();

    
    public abstract function getContentType();

    
    public abstract function getCharset();

    
    public abstract function getContentDisposition();

    
    public abstract function getContentTransferEncoding();

    
    public function getFilename()
    {
        return null;
    }

    
    public abstract function isMime();

    
    public abstract function getContentId();

    
    public function getResourceHandle()
    {
        return StreamWrapper::getResource($this->getStream());
    }

    
    public function getStream()
    {
        if ($this->stream === null) {
            return $this->streamFactory->newMessagePartStream($this);
        }
        $this->stream->rewind();
        return $this->stream;
    }

    
    public function setCharsetOverride($charsetOverride, $onlyIfNoCharset = false)
    {
        if (!$onlyIfNoCharset || $this->getCharset() === null) {
            $this->charsetOverride = $charsetOverride;
        }
    }

    
    public function getContentResourceHandle($charset = MailMimeParser::DEFAULT_CHARSET)
    {
        trigger_error("getContentResourceHandle is deprecated since version 1.2.1", E_USER_DEPRECATED);
        $stream = $this->getContentStream($charset);
        if ($stream !== null) {
            return StreamWrapper::getResource($stream);
        }
        return null;
    }

    
    public function getContentStream($charset = MailMimeParser::DEFAULT_CHARSET)
    {
        if ($this->hasContent()) {
            $tr = ($this->ignoreTransferEncoding) ? '' : $this->getContentTransferEncoding();
            $ch = ($this->charsetOverride !== null) ? $this->charsetOverride : $this->getCharset();
            return $this->partStreamFilterManager->getContentStream(
                $tr,
                $ch,
                $charset
            );
        }
        return null;
    }

    
    public function getBinaryContentStream()
    {
        if ($this->hasContent()) {
            $tr = ($this->ignoreTransferEncoding) ? '' : $this->getContentTransferEncoding();
            return $this->partStreamFilterManager->getBinaryStream($tr);
        }
        return null;
    }

    
    public function getBinaryContentResourceHandle()
    {
        $stream = $this->getBinaryContentStream();
        if ($stream !== null) {
            return StreamWrapper::getResource($stream);
        }
        return null;
    }

    
    public function saveContent($filenameResourceOrStream)
    {
        $resourceOrStream = $filenameResourceOrStream;
        if (is_string($filenameResourceOrStream)) {
            $resourceOrStream = fopen($filenameResourceOrStream, 'w+');
        }

        $stream = Psr7\stream_for($resourceOrStream);
        Psr7\copy_to_stream($this->getBinaryContentStream(), $stream);

        if (!is_string($filenameResourceOrStream)
            && !($filenameResourceOrStream instanceof StreamInterface)) {
            
            
            $stream->detach();
        }
    }

    
    public function getContent($charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $stream = $this->getContentStream($charset);
        if ($stream !== null) {
            return $stream->getContents();
        }
        return null;
    }

    
    public function getParent()
    {
        return $this->parent;
    }

    
    public function attachContentStream(StreamInterface $stream, $streamCharset = MailMimeParser::DEFAULT_CHARSET)
    {
        if ($this->contentStream !== null && $this->contentStream !== $stream) {
            $this->contentStream->close();
        }
        $this->contentStream = $stream;
        $ch = ($this->charsetOverride !== null) ? $this->charsetOverride : $this->getCharset();
        if ($ch !== null && $streamCharset !== $ch) {
            $this->charsetOverride = $streamCharset;
        }
        $this->ignoreTransferEncoding = true;
        $this->partStreamFilterManager->setStream($stream);
        $this->onChange();
    }

    
    public function detachContentStream()
    {
        $this->contentStream = null;
        $this->partStreamFilterManager->setStream(null);
        $this->onChange();
    }

    
    public function setContent($resource, $charset = MailMimeParser::DEFAULT_CHARSET)
    {
        $stream = Psr7\stream_for($resource);
        $this->attachContentStream($stream, $charset);
        
    }

    
    public function save($filenameResourceOrStream)
    {
        $resourceOrStream = $filenameResourceOrStream;
        if (is_string($filenameResourceOrStream)) {
            $resourceOrStream = fopen($filenameResourceOrStream, 'w+');
        }

        $partStream = $this->getStream();
        $partStream->rewind();
        $stream = Psr7\stream_for($resourceOrStream);
        Psr7\copy_to_stream($partStream, $stream);

        if (!is_string($filenameResourceOrStream)
            && !($filenameResourceOrStream instanceof StreamInterface)) {
            
            
            $stream->detach();
        }
    }

    
    public function __toString()
    {
        return $this->getStream()->getContents();
    }
}
