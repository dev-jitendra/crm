<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class AuthenticationProvider extends Entity
{
    public const ENTITY_TYPE = 'AuthenticationProvider';

    public function getMethod(): ?string
    {
        return $this->get('method');
    }
}
