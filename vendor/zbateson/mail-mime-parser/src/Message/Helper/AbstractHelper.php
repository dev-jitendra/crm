<?php

namespace ZBateson\MailMimeParser\Message\Helper;

use ZBateson\MailMimeParser\Message\Part\Factory\MimePartFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\PartBuilderFactory;
use ZBateson\MailMimeParser\Message\Part\Factory\UUEncodedPartFactory;


abstract class AbstractHelper
{
    
    protected $mimePartFactory;

    
    protected $uuEncodedPartFactory;

    
    protected $partBuilderFactory;

    
    public function __construct(
        MimePartFactory $mimePartFactory,
        UUEncodedPartFactory $uuEncodedPartFactory,
        PartBuilderFactory $partBuilderFactory
    ) {
        $this->mimePartFactory = $mimePartFactory;
        $this->uuEncodedPartFactory = $uuEncodedPartFactory;
        $this->partBuilderFactory = $partBuilderFactory;
    }
}
