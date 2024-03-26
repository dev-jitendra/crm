<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\HeaderPart;
use ZBateson\MailMimeParser\Header\Part\Token;
use ZBateson\MailMimeParser\Header\Part\AddressGroupPart;


class AddressConsumer extends AbstractConsumer
{
    
    protected function getSubConsumers()
    {
        return [
            $this->consumerService->getAddressGroupConsumer(),
            $this->consumerService->getCommentConsumer(),
            $this->consumerService->getQuotedStringConsumer(),
        ];
    }
    
    
    public function getTokenSeparators()
    {
        return ['<', '>', ',', ';', '\s+'];
    }
    
    
    protected function isEndToken($token)
    {
        return ($token === ',' || $token === ';');
    }
    
    
    protected function isStartToken($token)
    {
        return true;
    }
    
    
    private function processSinglePart(HeaderPart $part, &$strName, &$strValue)
    {
        $pValue = $part->getValue();
        if ($part instanceof Token) {
            if ($pValue === '<') {
                $strName = $strValue;
                $strValue = '';
                return;
            } elseif ($pValue === '>') {
                return;
            }
        }
        $strValue .= $pValue;
    }
    
    
    protected function processParts(array $parts)
    {
        $strName = '';
        $strValue = '';
        foreach ($parts as $part) {
            if ($part instanceof AddressGroupPart) {
                return [
                    $this->partFactory->newAddressGroupPart(
                        $part->getAddresses(),
                        $strValue
                    )
                ];
            }
            $this->processSinglePart($part, $strName, $strValue);
        }
        return [$this->partFactory->newAddressPart($strName, $strValue)];
    }
}
