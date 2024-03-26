<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;

class LayoutSet extends Entity
{
    public const ENTITY_TYPE = 'LayoutSet';

    
    public function getLayoutList(): array
    {
        return $this->get('layoutList') ?? [];
    }
}
