<?php

namespace ZBateson\MailMimeParser\Message;

use Psr\Http\Message\StreamInterface;
use ZBateson\MailMimeParser\Message;
use ZBateson\MailMimeParser\Message\Helper\MessageHelperService;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;
use ZBateson\MailMimeParser\Message\Part\Factory\MimePartFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartStreamFilterManagerFactory;
use ZBateson\MailMimeParser\Message\PartFilterFactory;
use ZBateson\MailMimeParser\Stream\StreamFactory;


class MessageFactory extends MimePartFactory
{
    
    protected $messageHelperService;

    
    public function __construct(
        StreamFactory $sdf,
        PartStreamFilterManagerFactory $psf,
        PartFilterFactory $pf,
        MessageHelperService $mhs
    ) {
        parent::__construct($sdf, $psf, $pf);
        $this->messageHelperService = $mhs;
    }

    
    public function newInstance(PartBuilder $partBuilder, StreamInterface $stream = null)
    {
        $contentStream = null;
        if ($stream !== null) {
            $contentStream = $this->streamFactory->getLimitedContentStream($stream, $partBuilder);
        }
        return new Message(
            $this->partStreamFilterManagerFactory->newInstance(),
            $this->streamFactory,
            $this->partFilterFactory,
            $partBuilder,
            $this->messageHelperService,
            $stream,
            $contentStream
        );
    }
}
