<?php

namespace ZBateson\MailMimeParser\Message\Part;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Header\ParameterHeader;
use ZBateson\MailMimeParser\Stream\StreamFactory;
use ZBateson\MailMimeParser\Message\PartFilterFactory;
use ZBateson\MailMimeParser\Header\HeaderContainer;


abstract class ParentHeaderPart extends ParentPart
{
    
    protected $headerContainer;

    
    public function __construct(
        PartStreamFilterManager $partStreamFilterManager,
        StreamFactory $streamFactory,
        PartFilterFactory $partFilterFactory,
        PartBuilder $partBuilder,
        StreamInterface $stream = null,
        StreamInterface $contentStream = null
    ) {
        parent::__construct(
            $partStreamFilterManager,
            $streamFactory,
            $partFilterFactory,
            $partBuilder,
            $stream,
            $contentStream
        );
        $this->headerContainer = $partBuilder->getHeaderContainer();
    }

    
    public function getHeader($name, $offset = 0)
    {
        return $this->headerContainer->get($name, $offset);
    }

    
    public function getAllHeaders()
    {
        return $this->headerContainer->getHeaderObjects();
    }

    
    public function getAllHeadersByName($name)
    {
        return $this->headerContainer->getAll($name);
    }

    
    public function getRawHeaders()
    {
        return $this->headerContainer->getHeaders();
    }

    
    public function getRawHeaderIterator()
    {
        return $this->headerContainer->getIterator();
    }

    
    public function getHeaderValue($name, $defaultValue = null)
    {
        $header = $this->getHeader($name);
        if ($header !== null) {
            return $header->getValue();
        }
        return $defaultValue;
    }

    
    public function getHeaderParameter($header, $param, $defaultValue = null)
    {
        $obj = $this->getHeader($header);
        if ($obj && $obj instanceof ParameterHeader) {
            return $obj->getValueFor($param, $defaultValue);
        }
        return $defaultValue;
    }

    
    public function setRawHeader($name, $value, $offset = 0)
    {
        $this->headerContainer->set($name, $value, $offset);
        $this->onChange();
    }

    
    public function addRawHeader($name, $value)
    {
        $this->headerContainer->add($name, $value);
        $this->onChange();
    }

    
    public function removeHeader($name)
    {
        $this->headerContainer->removeAll($name);
        $this->onChange();
    }

    
    public function removeSingleHeader($name, $offset = 0)
    {
        $this->headerContainer->remove($name, $offset);
        $this->onChange();
    }
}
