<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\HeaderPart;
use ZBateson\MailMimeParser\Header\Part\Token;
use Iterator;


class SubjectConsumer extends GenericConsumer
{
    
    protected function getSubConsumers()
    {
        return [];
    }

    
    protected function getPartForToken($token, $isLiteral)
    {
        if ($isLiteral) {
            return $this->partFactory->newLiteralPart($token);
        } elseif (preg_match('/^\s+$/', $token)) {
            if (preg_match('/^[\r\n]/', $token)) {
                return $this->partFactory->newToken(' ');
            }
            return $this->partFactory->newToken($token);
        }
        return $this->partFactory->newInstance($token);
    }

    
    protected function getTokenParts(Iterator $tokens)
    {
        return $this->getConsumerTokenParts($tokens);
    }

    
    protected function getTokenSplitPattern()
    {
        $sChars = implode('|', $this->getAllTokenSeparators());
        return '~(' . $sChars . ')~';
    }
}
