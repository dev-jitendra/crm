<?php

namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\AbstractConsumer;
use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\MimeLiteralPart;
use ZBateson\MailMimeParser\Header\Part\MimeLiteralPartFactory;


abstract class MimeEncodedHeader extends AbstractHeader
{
    
    protected $mimeLiteralPartFactory;

    
    public function __construct(
        MimeLiteralPartFactory $mimeLiteralPartFactory,
        ConsumerService $consumerService,
        $name,
        $value
    ) {
        $this->mimeLiteralPartFactory = $mimeLiteralPartFactory;
        parent::__construct($consumerService, $name, $value);
    }

    
    protected function setParseHeaderValue(AbstractConsumer $consumer)
    {
        $value = $this->rawValue;
        $matchp = '~^(\s*' . MimeLiteralPart::MIME_PART_PATTERN . '\s*)+$~';
        if (preg_match($matchp, $value)) {
            $p = $this->mimeLiteralPartFactory->newInstance($value);
            $value = $p->getValue();
        }
        $this->parts = $consumer($value);
    }
}
