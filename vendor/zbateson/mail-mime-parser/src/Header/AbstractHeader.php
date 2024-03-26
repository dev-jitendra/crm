<?php

namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\AbstractConsumer;
use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;


abstract class AbstractHeader
{
    
    protected $name;

    
    protected $parts;

    
    protected $rawValue;

    
    public function __construct(ConsumerService $consumerService, $name, $value)
    {
        $this->name = $name;
        $this->rawValue = $value;

        $consumer = $this->getConsumer($consumerService);
        $this->setParseHeaderValue($consumer);
    }

    
    abstract protected function getConsumer(ConsumerService $consumerService);

    
    protected function setParseHeaderValue(AbstractConsumer $consumer)
    {
        $this->parts = $consumer($this->rawValue);
    }

    
    public function getParts()
    {
        return $this->parts;
    }

    
    public function getValue()
    {
        if (!empty($this->parts)) {
            return $this->parts[0]->getValue();
        }
        return null;
    }

    
    public function getRawValue()
    {
        return $this->rawValue;
    }

    
    public function getName()
    {
        return $this->name;
    }

    
    public function __toString()
    {
        return "{$this->name}: {$this->rawValue}";
    }
}
