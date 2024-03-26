<?php


namespace Espo\Entities;

class Preferences extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'Preferences';

    
    public function getSmtpParams()
    {
        return null;
    }
}
