<?php

namespace ZBateson\MailMimeParser\Message\Part\Factory;

use ZBateson\MailMimeParser\Stream\StreamFactory;
use ZBateson\MailMimeParser\Message\Helper\MessageHelperService;
use ZBateson\MailMimeParser\Message\MessageFactory;
use ZBateson\MailMimeParser\Message\PartFilterFactory;


class PartFactoryService
{
    
    protected $partFilterFactory;

    
    protected $partStreamFilterManagerFactory;

    
    protected $streamFactory;

    
    protected $messageHelperService;
    
    
    public function __construct(
        PartFilterFactory $partFilterFactory,
        StreamFactory $streamFactory,
        PartStreamFilterManagerFactory $partStreamFilterManagerFactory,
        MessageHelperService $messageHelperService
    ) {
        $this->partFilterFactory = $partFilterFactory;
        $this->streamFactory = $streamFactory;
        $this->partStreamFilterManagerFactory = $partStreamFilterManagerFactory;
        $this->messageHelperService = $messageHelperService;
    }

    
    public function getMessageFactory()
    {
        return MessageFactory::getInstance(
            $this->streamFactory,
            $this->partStreamFilterManagerFactory,
            $this->partFilterFactory,
            $this->messageHelperService
        );
    }
    
    
    public function getMimePartFactory()
    {
        return MimePartFactory::getInstance(
            $this->streamFactory,
            $this->partStreamFilterManagerFactory,
            $this->partFilterFactory
        );
    }
    
    
    public function getNonMimePartFactory()
    {
        return NonMimePartFactory::getInstance(
            $this->streamFactory,
            $this->partStreamFilterManagerFactory
        );
    }
    
    
    public function getUUEncodedPartFactory()
    {
        return UUEncodedPartFactory::getInstance(
            $this->streamFactory,
            $this->partStreamFilterManagerFactory
        );
    }
}
