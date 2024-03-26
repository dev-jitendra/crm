<?php


namespace Espo\Entities;

use InvalidArgumentException;

use Espo\Core\ORM\Entity;

class PhoneNumber extends Entity
{
    public const ENTITY_TYPE = 'PhoneNumber';

    public const RELATION_ENTITY_PHONE_NUMBER = 'EntityPhoneNumber';

    
    protected function _setName($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException("Phone number can't be empty");
        }

        $this->setInContainer('name', $value);
    }

    public function getNumber(): string
    {
        return $this->get('name');
    }

    public function isOptedOut(): bool
    {
        return $this->get('optOut');
    }

    public function isInvalid(): bool
    {
        return $this->get('invalid');
    }

    public function getType(): ?string
    {
        return $this->get('type');
    }
}

