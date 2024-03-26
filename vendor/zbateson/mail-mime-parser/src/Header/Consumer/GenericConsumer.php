<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\HeaderPart;
use ZBateson\MailMimeParser\Header\Part\Token;


class GenericConsumer extends AbstractConsumer
{
    
    protected function getSubConsumers()
    {
        return [
            $this->consumerService->getCommentConsumer(),
            $this->consumerService->getQuotedStringConsumer(),
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
    
    
    private function shouldAddSpace(HeaderPart $nextPart, HeaderPart $lastPart)
    {
        return (!$lastPart->ignoreSpacesAfter() || !$nextPart->ignoreSpacesBefore());
    }
    
    
    private function addSpaceToRetParts(
        array $parts,
        array &$retParts,
        $curIndex,
        HeaderPart &$spacePart,
        HeaderPart $lastPart
    ) {
        $nextPart = $parts[$curIndex];
        if ($this->shouldAddSpace($nextPart, $lastPart)) {
            $retParts[] = $spacePart;
            $spacePart = null;
        }
    }
    
    
    private function addSpaces(array $parts, array &$retParts, $curIndex, HeaderPart &$spacePart = null)
    {
        $lastPart = end($retParts);
        if ($spacePart !== null && $curIndex < count($parts) && $parts[$curIndex]->getValue() !== '' && $lastPart !== false) {
            $this->addSpaceToRetParts($parts, $retParts, $curIndex, $spacePart, $lastPart);
        }
    }
    
    
    private function isSpaceToken(HeaderPart $part)
    {
        return ($part instanceof Token && $part->isSpace());
    }
    
    
    protected function filterIgnoredSpaces(array $parts)
    {
        $partsFiltered = array_values(array_filter($parts));
        $retParts = [];
        $spacePart = null;
        $count = count($partsFiltered);
        for ($i = 0; $i < $count; ++$i) {
            $part = $partsFiltered[$i];
            if ($this->isSpaceToken($part)) {
                $spacePart = $part;
                continue;
            }
            $this->addSpaces($partsFiltered, $retParts, $i, $spacePart);
            $retParts[] = $part;
        }
        
        return $retParts;
    }
    
    
    protected function processParts(array $parts)
    {
        $strValue = '';
        $filtered = $this->filterIgnoredSpaces($parts);
        foreach ($filtered as $part) {
            $strValue .= $part->getValue();
        }
        return [$this->partFactory->newLiteralPart($strValue)];
    }
}
