<?php

namespace ZBateson\MailMimeParser\Header;

use ZBateson\MailMimeParser\Header\Consumer\AbstractConsumer;
use ZBateson\MailMimeParser\Header\Consumer\ConsumerService;
use ZBateson\MailMimeParser\Header\Part\AddressPart;
use ZBateson\MailMimeParser\Header\Part\AddressGroupPart;


class AddressHeader extends AbstractHeader
{
    
    protected $addresses = [];
    
    
    protected $groups = [];
    
    
    protected function getConsumer(ConsumerService $consumerService)
    {
        return $consumerService->getAddressBaseConsumer();
    }
    
    
    protected function setParseHeaderValue(AbstractConsumer $consumer)
    {
        parent::setParseHeaderValue($consumer);
        foreach ($this->parts as $part) {
            if ($part instanceof AddressPart) {
                $this->addresses[] = $part;
            } elseif ($part instanceof AddressGroupPart) {
                $this->addresses = array_merge($this->addresses, $part->getAddresses());
                $this->groups[] = $part;
            }
        }
    }
    
    
    public function getAddresses()
    {
        return $this->addresses;
    }
    
    
    public function getGroups()
    {
        return $this->groups;
    }
    
    
    public function hasAddress($email)
    {
        foreach ($this->addresses as $addr) {
            if (strcasecmp($addr->getEmail(), $email) === 0) {
                return true;
            }
        }
        return false;
    }

    
    public function getEmail()
    {
        return $this->getValue();
    }

    
    public function getPersonName()
    {
        if (!empty($this->parts)) {
            return $this->parts[0]->getName();
        }
        return null;
    }
}
