<?php

namespace ZBateson\MailMimeParser\Header\Consumer;


class QuotedStringConsumer extends GenericConsumer
{
    
    public function getSubConsumers()
    {
        return [];
    }
    
    
    protected function isStartToken($token)
    {
        return ($token === '"');
    }
    
    
    protected function isEndToken($token)
    {
        return ($token === '"');
    }
    
    
    protected function getTokenSeparators()
    {
        return ['\"'];
    }
    
    
    protected function getPartForToken($token, $isLiteral)
    {
        return $this->partFactory->newLiteralPart($token);
    }
}
