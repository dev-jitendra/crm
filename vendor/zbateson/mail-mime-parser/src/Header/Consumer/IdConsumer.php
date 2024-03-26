<?php

namespace ZBateson\MailMimeParser\Header\Consumer;


class IdConsumer extends GenericConsumer
{
    
    public function getTokenSeparators()
    {
        return ['\s+', '<', '>'];
    }
    
    
    protected function isEndToken($token)
    {
        return ($token === '>');
    }
    
    
    protected function isStartToken($token)
    {
        return ($token === '<');
    }
}
