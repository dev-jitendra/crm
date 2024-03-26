<?php


namespace Espo\Modules\Crm\Entities;

use Espo\Core\Field\Address;
use Espo\Core\Field\EmailAddressGroup;
use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\Field\PhoneNumberGroup;
use Espo\Core\ORM\Entity;

class Account extends Entity
{
    public const ENTITY_TYPE = 'Account';

    public const TYPE_CUSTOMER = 'Customer';
    public const TYPE_PARTNER = 'Partner';
    public const TYPE_RESELLER = 'Reseller';

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function setName(?string $name): self
    {
        $this->set('name', $name);

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->get('emailAddress');
    }

    public function getEmailAddressGroup(): EmailAddressGroup
    {
        
        return $this->getValueObject('emailAddress');
    }

    public function getPhoneNumberGroup(): PhoneNumberGroup
    {
        
        return $this->getValueObject('phoneNumber');
    }

    public function setEmailAddressGroup(EmailAddressGroup $group): self
    {
        $this->setValueObject('emailAddress', $group);

        return $this;
    }

    public function setPhoneNumberGroup(PhoneNumberGroup $group): self
    {
        $this->setValueObject('phoneNumber', $group);

        return $this;
    }

    public function getBillingAddress(): Address
    {
        
        return $this->getValueObject('billingAddress');
    }

    public function setBillingAddress(Address $address): self
    {
        $this->setValueObject('billingAddress', $address);

        return $this;
    }

    public function getShippingAddress(): Address
    {
        
        return $this->getValueObject('shippingAddress');
    }

    public function setShippingAddress(Address $address): self
    {
        $this->setValueObject('shippingAddress', $address);

        return $this;
    }

    public function getAssignedUser(): ?Link
    {
        
        return $this->getValueObject('assignedUser');
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }
}
