<?php

namespace ZBateson\MailMimeParser\Message\Part;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Stream\StreamFactory;


class UUEncodedPart extends NonMimePart
{
    
    protected $mode = null;
    
    
    protected $filename = null;
    
    
    public function __construct(
        PartStreamFilterManager $partStreamFilterManager,
        StreamFactory $streamFactory,
        PartBuilder $partBuilder,
        StreamInterface $stream = null,
        StreamInterface $contentStream = null
    ) {
        parent::__construct($partStreamFilterManager, $streamFactory, $stream, $contentStream);
        $this->mode = intval($partBuilder->getProperty('mode'));
        $this->filename = $partBuilder->getProperty('filename');
    }
    
    
    public function getUnixFileMode()
    {
        return $this->mode;
    }
    
    
    public function getFilename()
    {
        return $this->filename;
    }

    
    public function setUnixFileMode($mode)
    {
        $this->mode = $mode;
        $this->onChange();
    }

    
    public function setFilename($filename)
    {
        $this->filename = $filename;
        $this->onChange();
    }
    
    
    public function isTextPart()
    {
        return false;
    }
    
    
    public function getContentType()
    {
        return 'application/octet-stream';
    }
    
    
    public function getCharset()
    {
        return null;
    }
    
    
    public function getContentDisposition()
    {
        return 'attachment';
    }
    
    
    public function getContentTransferEncoding()
    {
        return 'x-uuencode';
    }
}
