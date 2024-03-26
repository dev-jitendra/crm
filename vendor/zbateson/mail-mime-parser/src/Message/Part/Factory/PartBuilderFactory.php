<?php

namespace ZBateson\MailMimeParser\Message\Part\Factory;

use ZBateson\MailMimeParser\Header\HeaderFactory;
use ZBateson\MailMimeParser\Message\Part\PartBuilder;


class PartBuilderFactory
{
    
    protected $headerFactory;
    
    
    public function __construct(HeaderFactory $headerFactory)
    {
        $this->headerFactory = $headerFactory;
    }
    
    
    public function newPartBuilder(MessagePartFactory $messagePartFactory)
    {
        return new PartBuilder(
            $messagePartFactory,
            $this->headerFactory->newHeaderContainer()
        );
    }
}
