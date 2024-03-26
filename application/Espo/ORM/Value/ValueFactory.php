<?php


namespace Espo\ORM\Value;

use Espo\ORM\Entity;

interface ValueFactory
{
    
    public function isCreatableFromEntity(Entity $entity, string $field): bool;

    
    public function createFromEntity(Entity $entity, string $field): ?object;
}
