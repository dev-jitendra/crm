<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use Iterator;


class AddressBaseConsumer extends AbstractConsumer
{
    
    protected function getSubConsumers()
    {
        return [
            $this->consumerService->getAddressConsumer()
        ];
    }
    
    
    protected function getTokenSeparators()
    {
        return [];
    }
    
    
    protected function advanceToNextToken(Iterator $tokens, $isStartToken)
    {
        if ($isStartToken) {
            return;
        }
        parent::advanceToNextToken($tokens, $isStartToken);
    }
    
    
    protected function isEndToken($token)
    {
        return false;
    }
    
    
    protected function isStartToken($token)
    {
        return false;
    }

    
    protected function getTokenParts(Iterator $tokens)
    {
        return $this->getConsumerTokenParts($tokens);
    }
    
    
    protected function getPartForToken($token, $isLiteral)
    {
        return null;
    }
}
