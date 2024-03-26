<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class LayoutRecord extends Entity
{
    public const ENTITY_TYPE = 'LayoutRecord';

    public function getData(): mixed
    {
        return $this->get('data');
    }
}
