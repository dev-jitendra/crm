<?php


namespace Espo\Entities;

class GroupEmailFolder extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'GroupEmailFolder';

    public function getOrder(): ?int
    {
        return $this->get('order');
    }
}
