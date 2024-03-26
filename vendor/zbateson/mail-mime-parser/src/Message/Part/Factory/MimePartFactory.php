<?php

namespace ZBateson\MailMimeParser\Message\Part\Factory;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Stream\StreamFactory;
use ZBateson\MailMimeParser\Message\PartFilterFactory;
use ZBateson\MailMimeParser\Message\Part\MimePart;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;


class MimePartFactory extends MessagePartFactory
{
    
    protected $partFilterFactory;

    
    public function __construct(
        StreamFactory $sdf,
        PartStreamFilterManagerFactory $psf,
        PartFilterFactory $pf
    ) {
        parent::__construct($sdf, $psf);
        $this->partFilterFactory = $pf;
    }

    
    public function newInstance(PartBuilder $partBuilder, StreamInterface $messageStream = null)
    {
        $partStream = null;
        $contentStream = null;
        if ($messageStream !== null) {
            $partStream = $this->streamFactory->getLimitedPartStream($messageStream, $partBuilder);
            $contentStream = $this->streamFactory->getLimitedContentStream($messageStream, $partBuilder);
        }
        return new MimePart(
            $this->partStreamFilterManagerFactory->newInstance(),
            $this->streamFactory,
            $this->partFilterFactory,
            $partBuilder,
            $partStream,
            $contentStream
        );
    }
}
