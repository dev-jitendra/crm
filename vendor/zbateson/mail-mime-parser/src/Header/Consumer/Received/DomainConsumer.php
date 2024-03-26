<?php

namespace ZBateson\MailMimeParser\Header\Consumer\Received;

use ZBateson\MailMimeParser\Header\Part\CommentPart;


class DomainConsumer extends GenericReceivedConsumer
{
    
    protected function isEndToken($token)
    {
        if ($token === ')') {
            return true;
        }
        return parent::isEndToken($token);
    }

    
    private function matchHostPart($value, &$hostname, &$address) {
        $matches = [];
        $pattern = '~^(?P<name>[a-z0-9\-]+\.[a-z0-9\-\.]+)?\s*(\[(IPv[64])?(?P<addr>[a-f\d\.\:]+)\])?$~i';
        if (preg_match($pattern, $value, $matches)) {
            if (!empty($matches['name'])) {
                $hostname = $matches['name'];
            }
            if (!empty($matches['addr'])) {
                $address = $matches['addr'];
            }
            return true;
        }
        return false;
    }

    
    protected function processParts(array $parts)
    {
        $ehloName = null;
        $hostname = null;
        $address = null;
        $commentPart = null;

        $filtered = $this->filterIgnoredSpaces($parts);
        foreach ($filtered as $part) {
            if ($part instanceof CommentPart) {
                $commentPart = $part;
                continue;
            }
            $ehloName .= $part->getValue();
        }

        $strValue = $ehloName;
        if ($commentPart !== null && $this->matchHostPart($commentPart->getComment(), $hostname, $address)) {
            $strValue .= ' (' . $commentPart->getComment() . ')';
            $commentPart = null;
        }

        $domainPart = $this->partFactory->newReceivedDomainPart(
            $this->getPartName(),
            $strValue,
            $ehloName,
            $hostname,
            $address
        );
        return array_filter([ $domainPart, $commentPart ]);
    }
}
