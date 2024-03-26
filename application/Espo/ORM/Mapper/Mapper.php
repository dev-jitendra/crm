<?php


namespace Espo\ORM\Mapper;

use Espo\ORM\Entity;
use Espo\ORM\Collection;
use Espo\ORM\Query\Select;

interface Mapper
{
    
    public function selectOne(Select $select): ?Entity;

    
    public function select(Select $select): Collection;

    
    public function count(Select $select): int;

    
    public function insert(Entity $entity): void;

    
    public function massInsert(Collection $collection): void;

    
    public function update(Entity $entity): void;

    
    public function delete(Entity $entity): void;

    
    public function insertOnDuplicateUpdate(Entity $entity, array $onDuplicateUpdateAttributeList): void;
}
