<?php


namespace Espo\Entities;

use Espo\Core\Field\DateTime;

class TwoFactorCode extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'TwoFactorCode';

    public function isActive(): bool
    {
        return $this->get('isActive');
    }

    public function getCreatedAt(): DateTime
    {
        
        return $this->getValueObject('createdAt');
    }

    public function getCode(): string
    {
        return $this->get('code');
    }

    public function getAttemptsLeft(): int
    {
        return $this->get('attemptsLeft');
    }

    public function setInactive(): void
    {
        $this->set('isActive', false);
    }

    public function decrementAttemptsLeft(): void
    {
        $this->set('attemptsLeft', $this->getAttemptsLeft() - 1);
    }
}
