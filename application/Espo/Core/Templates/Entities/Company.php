<?php


namespace Espo\Core\Templates\Entities;

use Espo\Core\Field\Address;
use Espo\Core\Field\EmailAddressGroup;
use Espo\Core\Field\PhoneNumberGroup;
use Espo\Core\ORM\Entity;

class Company extends Entity
{
    public const TEMPLATE_TYPE = 'Company';

    public function getEmailAddressGroup(): EmailAddressGroup
    {
        
        return $this->getValueObject('emailAddress');
    }

    public function getPhoneNumberGroup(): PhoneNumberGroup
    {
        
        return $this->getValueObject('phoneNumber');
    }

    public function setEmailAddressGroup(EmailAddressGroup $group): void
    {
        $this->setValueObject('emailAddress', $group);
    }

    public function setPhoneNumberGroup(PhoneNumberGroup $group): void
    {
        $this->setValueObject('phoneNumber', $group);
    }

    public function getBillingAddress(): Address
    {
        
        return $this->getValueObject('billingAddress');
    }

    public function setBillingAddress(Address $address): void
    {
        $this->setValueObject('billingAddress', $address);
    }

    public function getShippingAddress(): Address
    {
        
        return $this->getValueObject('shippingAddress');
    }

    public function setShippingAddress(Address $address): void
    {
        $this->setValueObject('shippingAddress', $address);
    }
}

