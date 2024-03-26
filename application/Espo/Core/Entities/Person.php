<?php


namespace Espo\Core\Entities;

use Espo\Core\Field\Address;
use Espo\Core\Field\EmailAddressGroup;
use Espo\Core\Field\PhoneNumberGroup;
use Espo\Core\ORM\Entity;
use Espo\Core\ORM\Helper;

use Espo\ORM\EntityManager;
use Espo\ORM\Value\ValueAccessorFactory;

class Person extends Entity
{
    private Helper $helper;

    public function __construct(
        string $entityType,
        array $defs,
        EntityManager $entityManager,
        Helper $helper,
        ?ValueAccessorFactory $valueAccessorFactory = null
    ) {
        parent::__construct($entityType, $defs, $entityManager, $valueAccessorFactory);

        $this->helper = $helper;
    }

    
    protected function _setLastName($value)
    {
        $this->setInContainer('lastName', $value);

        $name = $this->helper->formatPersonName($this, 'name');

        $this->setInContainer('name', $name);
    }

    
    protected function _setFirstName($value)
    {
        $this->setInContainer('firstName', $value);

        $name = $this->helper->formatPersonName($this, 'name');

        $this->setInContainer('name', $name);
    }

    
    protected function _setMiddleName($value)
    {
        $this->setInContainer('middleName', $value);

        $name = $this->helper->formatPersonName($this, 'name');

        $this->setInContainer('name', $name);
    }

    
    public function getEmailAddress(): ?string
    {
        return $this->get('emailAddress');
    }

    
    public function getPhoneNumber(): ?string
    {
        return $this->get('phoneNumber');
    }

    public function getEmailAddressGroup(): EmailAddressGroup
    {
        
        return $this->getValueObject('emailAddress');
    }

    public function getPhoneNumberGroup(): PhoneNumberGroup
    {
        
        return $this->getValueObject('phoneNumber');
    }

    public function getName(): ?string
    {
        return $this->get('name');
    }

    public function getFirstName(): ?string
    {
        return $this->get('firstName');
    }

    public function getLastName(): ?string
    {
        return $this->get('lastName');
    }

    public function getMiddleName(): ?string
    {
        return $this->get('middleName');
    }

    public function setFirstName(?string $firstName): self
    {
        $this->set('firstName', $firstName);

        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        $this->set('lastName', $lastName);

        return $this;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->set('middleName', $middleName);

        return $this;
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

    public function getAddress(): Address
    {
        
        return $this->getValueObject('address');
    }

    public function setAddress(Address $address): self
    {
        $this->setValueObject('address', $address);

        return $this;
    }
}
