<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\CommentPart;


class IdBaseConsumer extends AbstractConsumer
{
    
    protected function getSubConsumers()
    {
        return [
            $this->consumerService->getCommentConsumer(),
            $this->consumerService->getQuotedStringConsumer(),
            $this->consumerService->getIdConsumer()
        ];
    }
    
    
    protected function getTokenSeparators()
    {
        return ['\s+'];
    }

    
    protected function isEndToken($token)
    {
        return false;
    }
    
    
    protected function isStartToken($token)
    {
        return false;
    }
    
    
    protected function getPartForToken($token, $isLiteral)
    {
        if (preg_match('/^\s+$/', $token)) {
            return null;
        }
        return $this->partFactory->newLiteralPart($token);
    }

    
    protected function processParts(array $parts)
    {
        return array_values(array_filter($parts, function ($part) {
            if (empty($part) || $part instanceof CommentPart) {
                return false;
            }
            return true;
        }));
    }
}
