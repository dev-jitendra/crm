<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

use InvalidArgumentException;

class EmailAddress extends Entity
{
    public const ENTITY_TYPE = 'EmailAddress';

    public const RELATION_ENTITY_EMAIL_ADDRESS = 'EntityEmailAddress';

    
    protected function _setName($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException("Not valid email address '{$value}'");
        }

        $this->setInContainer('name', $value);

        $this->set('lower', strtolower($value));
    }

    public function getAddress(): string
    {
        return $this->get('name');
    }

    public function getLower(): string
    {
        return $this->get('lower');
    }

    public function isOptedOut(): bool
    {
        return $this->get('optOut');
    }

    public function isInvalid(): bool
    {
        return $this->get('invalid');
    }
}
