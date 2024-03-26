<?php

namespace ZBateson\MailMimeParser\Message\Part\Factory;

use ZBateson\MailMimeParser\Stream\StreamFactory;
use ZBateson\MailMimeParser\Message\Part\PartStreamFilterManager;


class PartStreamFilterManagerFactory
{
    
    protected $streamFactory;
    
    
    public function __construct(StreamFactory $streamFactory) {
        $this->streamFactory = $streamFactory;
    }
    
    
    public function newInstance()
    {
        return new PartStreamFilterManager($this->streamFactory);
    }
}
