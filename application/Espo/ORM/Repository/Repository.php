<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Entity;


interface Repository
{
    
    public function getNew(): Entity;

    
    public function getById(string $id): ?Entity;

    
    public function save(Entity $entity, array $options = []): void;

    
    public function remove(Entity $entity, array $options = []): void;
}
