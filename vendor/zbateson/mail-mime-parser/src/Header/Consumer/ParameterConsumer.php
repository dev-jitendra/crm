<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\Token;
use ZBateson\MailMimeParser\Header\Part\MimeLiteralPart;
use ZBateson\MailMimeParser\Header\Part\SplitParameterToken;
use ArrayObject;


class ParameterConsumer extends GenericConsumer
{
    
    protected function getTokenSeparators()
    {
        return [';', '='];
    }

    
    protected function getTokenSplitPattern()
    {
        $sChars = implode('|', $this->getAllTokenSeparators());
        $mimePartPattern = MimeLiteralPart::MIME_PART_PATTERN_NO_QUOTES;
        return '~(' . $mimePartPattern . '|\\\\.|' . $sChars . ')~';
    }

    
    protected function getPartForToken($token, $isLiteral)
    {
        if ($isLiteral) {
            return $this->partFactory->newLiteralPart($token);
        }
        return $this->partFactory->newToken($token);
    }
    
    
    private function addToSplitPart(ArrayObject $splitParts, $name, $value, $index, $isEncoded)
    {
        $ret = null;
        if (!isset($splitParts[trim($name)])) {
            $ret = $this->partFactory->newSplitParameterToken($name);
            $splitParts[$name] = $ret;
        }
        $splitParts[$name]->addPart($value, $isEncoded, $index);
        return $ret;
    }
    
    
    private function getPartFor($strName, $strValue, ArrayObject $splitParts)
    {
        if ($strName === '') {
            return $this->partFactory->newMimeLiteralPart($strValue);
        } elseif (preg_match('~^\s*([^\*]+)\*(\d*)(\*)?$~', $strName, $matches)) {
            return $this->addToSplitPart(
                $splitParts,
                $matches[1],
                $strValue,
                $matches[2],
                (empty($matches[2]) || !empty($matches[3]))
            );
        }
        return $this->partFactory->newParameterPart($strName, $strValue);
    }

    
    private function processTokenPart(
        $tokenValue,
        ArrayObject $combined,
        ArrayObject $splitParts,
        &$strName,
        &$strCat
    ) {
        if ($tokenValue === ';') {
            $combined[] = $this->getPartFor($strName, $strCat, $splitParts);
            $strName = '';
            $strCat = '';
            return true;
        } elseif ($tokenValue === '=' && $strCat !== '') {
            $strName = $strCat;
            $strCat = '';
            return true;
        }
        return false;
    }
    
    
    private function finalizeParameterParts(ArrayObject $combined)
    {
        foreach ($combined as $key => $part) {
            if ($part instanceof SplitParameterToken) {
                $combined[$key] = $this->partFactory->newParameterPart(
                    $part->getName(),
                    $part->getValue(),
                    $part->getLanguage()
                );
            }
        }
        return $this->filterIgnoredSpaces($combined->getArrayCopy());
    }
    
    
    protected function processParts(array $parts)
    {
        $combined = new ArrayObject();
        $splitParts = new ArrayObject();
        $strCat = '';
        $strName = '';
        $parts[] = $this->partFactory->newToken(';');
        foreach ($parts as $part) {
            $pValue = $part->getValue();
            if ($part instanceof Token && $this->processTokenPart($pValue, $combined, $splitParts, $strName, $strCat)) {
                continue;
            }
            $strCat .= $pValue;
        }
        return $this->finalizeParameterParts($combined);
    }
}
