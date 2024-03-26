<?php

namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Consumer\AbstractConsumer;
use ZBateson\MailMimeParser\Header\Part\ParameterPart;


class ParameterHeader extends AbstractHeader
{
    
    protected $parameters = [];
    
    
    protected function getConsumer(ConsumerService $consumerService)
    {
        return $consumerService->getParameterConsumer();
    }
    
    
    protected function setParseHeaderValue(AbstractConsumer $consumer)
    {
        parent::setParseHeaderValue($consumer);
        foreach ($this->parts as $part) {
            if ($part instanceof ParameterPart) {
                $this->parameters[strtolower($part->getName())] = $part;
            }
        }
    }
    
    
    public function hasParameter($name)
    {
        return isset($this->parameters[strtolower($name)]);
    }
    
    
    public function getValueFor($name, $defaultValue = null)
    {
        if (!$this->hasParameter($name)) {
            return $defaultValue;
        }
        return $this->parameters[strtolower($name)]->getValue();
    }
}
