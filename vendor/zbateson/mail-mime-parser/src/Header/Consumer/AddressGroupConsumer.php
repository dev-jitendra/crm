<?php

namespace ZBateson\MailMimeParser\Header\Consumer;

use ZBateson\MailMimeParser\Header\Part\AddressGroupPart;


class AddressGroupConsumer extends AddressBaseConsumer
{
    
    public function getTokenSeparators()
    {
        return [':', ';'];
    }
    
    
    protected function isEndToken($token)
    {
        return ($token === ';');
    }
    
    
    protected function isStartToken($token)
    {
        return ($token === ':');
    }
    
    
    protected function processParts(array $parts)
    {
        $emails = [];
        foreach ($parts as $part) {
            if ($part instanceof AddressGroupPart) {
                $emails = array_merge($emails, $part->getAddresses());
                continue;
            }
            $emails[] = $part;
        }
        $group = $this->partFactory->newAddressGroupPart($emails);
        return [$group];
    }
}
