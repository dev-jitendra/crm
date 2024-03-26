<?php

namespace ZBateson\MailMimeParser\Header\Consumer\Received;

use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\HeaderPartFactory;
use ZBateson\MailMimeParser\Header\Consumer\GenericConsumer;
use ZBateson\MailMimeParser\Header\Part\CommentPart;


class GenericReceivedConsumer extends GenericConsumer
{
    
    protected $partName;

    
    public function __construct(ConsumerService $consumerService, HeaderPartFactory $partFactory, $partName)
    {
        parent::__construct($consumerService, $partFactory);
        $this->partName = $partName;
    }

    
    protected function getPartName()
    {
        return $this->partName;
    }

    
    protected function getSubConsumers()
    {
        return [ $this->consumerService->getCommentConsumer() ];
    }

    
    protected function isStartToken($token)
    {
        $pattern = '/^\s*(' . preg_quote($this->getPartName(), '/') . ')\s*$/i';
        return (preg_match($pattern, $token) === 1);
    }

    
    protected function isEndToken($token)
    {
        return (preg_match('/^\s*(from|by|via|with|id|for|;)\s*$/i', $token) === 1);
    }

    
    protected function getTokenSeparators()
    {
        return [
            '\s+',
            '(\A\s*)?(?i)' . preg_quote($this->getPartName(), '/') . '(?-i)\s+'
        ];
    }

    
    protected function processParts(array $parts)
    {
        $strValue = '';
        $ret = [];
        $filtered = $this->filterIgnoredSpaces($parts);
        foreach ($filtered as $part) {
            if ($part instanceof CommentPart) {
                $ret[] = $part;
                continue;    
            }
            $strValue .= $part->getValue();
        }
        array_unshift($ret, $this->partFactory->newReceivedPart($this->getPartName(), $strValue));
        return $ret;
    }
}
