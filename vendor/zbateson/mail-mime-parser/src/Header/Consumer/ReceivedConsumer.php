<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\Token;
use Iterator;


class ReceivedConsumer extends AbstractConsumer
{
    
    protected function getTokenSeparators()
    {
        return [];
    }

    
    protected function isEndToken($token)
    {
        return false;
    }

    
    protected function isStartToken($token)
    {
        return false;
    }

    
    protected function getSubConsumers()
    {
        return [
            $this->consumerService->getSubReceivedConsumer('from'),
            $this->consumerService->getSubReceivedConsumer('by'),
            $this->consumerService->getSubReceivedConsumer('via'),
            $this->consumerService->getSubReceivedConsumer('with'),
            $this->consumerService->getSubReceivedConsumer('id'),
            $this->consumerService->getSubReceivedConsumer('for'),
            $this->consumerService->getSubReceivedConsumer('date'),
            $this->consumerService->getCommentConsumer()
        ];
    }

    
    protected function getTokenSplitPattern()
    {
        $sChars = implode('|', $this->getAllTokenSeparators());
        return '~(' . $sChars . ')~';
    }

    
    protected function advanceToNextToken(Iterator $tokens, $isStartToken)
    {
        if ($isStartToken) {
            $tokens->next();
        } elseif ($tokens->valid() && !$this->isEndToken($tokens->current())) {
            foreach ($this->getSubConsumers() as $consumer) {
                if ($consumer->isStartToken($tokens->current())) {
                    return;
                }
            }
            $tokens->next();
        }
    }

    
    protected function processParts(array $parts)
    {
        $ret = [];
        foreach ($parts as $part) {
            if ($part instanceof Token) {
                continue;
            }
            $ret[] = $part;
        }
        return $ret;
    }
}
