<?php

namespace ZBateson\MailMimeParser\Message\Part;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Header\HeaderContainer;
use ZBateson\MailMimeParser\Message\Part\Factory\MessagePartFactory;


class PartBuilder
{
    
    private $streamPartStartPos = 0;
    
    
    private $streamPartEndPos = 0;
    
    
    private $streamContentStartPos = 0;
    
    
    private $streamContentEndPos = 0;

    
    private $messagePartFactory;
    
    
    private $endBoundaryFound = false;
    
    
    private $parentBoundaryFound = false;
    
    
    private $mimeBoundary = false;
    
    
    private $headerContainer;
    
    
    private $children = [];
    
    
    private $parent = null;
    
    
    private $properties = [];

    
    public function __construct(
        MessagePartFactory $mpf,
        HeaderContainer $headerContainer
    ) {
        $this->messagePartFactory = $mpf;
        $this->headerContainer = $headerContainer;
    }
    
    
    public function addHeader($name, $value)
    {
        $this->headerContainer->add($name, $value);
    }
    
    
    public function getHeaderContainer()
    {
        return $this->headerContainer;
    }
    
    
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }
    
    
    public function getProperty($name)
    {
        if (!isset($this->properties[$name])) {
            return null;
        }
        return $this->properties[$name];
    }
    
    
    public function addChild(PartBuilder $partBuilder)
    {
        $partBuilder->parent = $this;
        
        if (!$this->endBoundaryFound) {
            $this->children[] = $partBuilder;
        }
    }
    
    
    public function getChildren()
    {
        return $this->children;
    }
    
    
    public function getParent()
    {
        return $this->parent;
    }
    
    
    public function isMime()
    {
        return ($this->headerContainer->exists('Content-Type') ||
            $this->headerContainer->exists('Mime-Version'));
    }
    
    
    public function getContentType()
    {
        return $this->headerContainer->get('Content-Type');
    }
    
    
    public function getMimeBoundary()
    {
        if ($this->mimeBoundary === false) {
            $this->mimeBoundary = null;
            $contentType = $this->getContentType();
            if ($contentType !== null) {
                $this->mimeBoundary = $contentType->getValueFor('boundary');
            }
        }
        return $this->mimeBoundary;
    }
    
    
    public function isMultiPart()
    {
        $contentType = $this->getContentType();
        if ($contentType !== null) {
            
            return (bool) (preg_match(
                '~multipart/.*~i',
                $contentType->getValue()
            ));
        }
        return false;
    }
    
    
    public function setEndBoundaryFound($line)
    {
        $boundary = $this->getMimeBoundary();
        if ($this->parent !== null && $this->parent->setEndBoundaryFound($line)) {
            $this->parentBoundaryFound = true;
            return true;
        } elseif ($boundary !== null) {
            if ($line === "--$boundary--") {
                $this->endBoundaryFound = true;
                return true;
            } elseif ($line === "--$boundary") {
                return true;
            }
        }
        return false;
    }
    
    
    public function isParentBoundaryFound()
    {
        return ($this->parentBoundaryFound);
    }
    
    
    public function setEof()
    {
        $this->parentBoundaryFound = true;
        if ($this->parent !== null) {
            $this->parent->parentBoundaryFound = true;
        }
    }
    
    
    public function canHaveHeaders()
    {
        return ($this->parent === null || !$this->parent->endBoundaryFound);
    }

    
    public function getStreamPartStartOffset()
    {
        if ($this->parent) {
            return $this->streamPartStartPos - $this->parent->streamPartStartPos;
        }
        return $this->streamPartStartPos;
    }

    
    public function getStreamPartLength()
    {
        return $this->streamPartEndPos - $this->streamPartStartPos;
    }

    
    public function getStreamContentStartOffset()
    {
        if ($this->parent) {
            return $this->streamContentStartPos - $this->parent->streamPartStartPos;
        }
        return $this->streamContentStartPos;
    }

    
    public function getStreamContentLength()
    {
        return $this->streamContentEndPos - $this->streamContentStartPos;
    }

    
    public function setStreamPartStartPos($streamPartStartPos)
    {
        $this->streamPartStartPos = $streamPartStartPos;
    }

    
    public function setStreamPartEndPos($streamPartEndPos)
    {
        $this->streamPartEndPos = $streamPartEndPos;
        if ($this->parent !== null) {
            $this->parent->setStreamPartEndPos($streamPartEndPos);
        }
    }

    
    public function setStreamContentStartPos($streamContentStartPos)
    {
        $this->streamContentStartPos = $streamContentStartPos;
    }

    
    public function setStreamPartAndContentEndPos($streamContentEndPos)
    {
        $this->streamContentEndPos = $streamContentEndPos;
        $this->setStreamPartEndPos($streamContentEndPos);
    }

    
    public function createMessagePart(StreamInterface $stream = null)
    {
        return $this->messagePartFactory->newInstance(
            $this,
            $stream
        );
    }
}
