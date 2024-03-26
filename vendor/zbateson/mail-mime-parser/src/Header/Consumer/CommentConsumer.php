<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\LiteralPart;
use ZBateson\MailMimeParser\Header\Part\CommentPart;
use Iterator;


class CommentConsumer extends GenericConsumer
{
    
    protected function getTokenSeparators()
    {
        return ['\(', '\)'];
    }
    
    
    protected function isStartToken($token)
    {
        return ($token === '(');
    }
    
    
    protected function isEndToken($token)
    {
        return ($token === ')');
    }
    
    
    protected function getPartForToken($token, $isLiteral)
    {
        return $this->partFactory->newToken($token);
    }
    
    
    protected function advanceToNextToken(Iterator $tokens, $isStartToken)
    {
        $tokens->next();
    }
    
    
    protected function processParts(array $parts)
    {
        $comment = '';
        foreach ($parts as $part) {
            
            if ($part instanceof CommentPart) {
                $comment .= '(' . $part->getComment() . ')';
            } elseif ($part instanceof LiteralPart) {
                $comment .= '"' . $part->getValue() . '"';
            } else {
                $comment .= $part->getValue();
            }
        }
        return [$this->partFactory->newCommentPart($comment)];
    }
}
