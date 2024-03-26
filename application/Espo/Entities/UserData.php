<?php


namespace Espo\Entities;

class UserData extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'UserData';

    public function getAuth2FA(): bool
    {
        return $this->get('auth2FA');
    }

    public function getAuth2FAMethod(): ?string
    {
        return $this->get('auth2FAMethod');
    }
}
