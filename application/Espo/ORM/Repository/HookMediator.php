<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Entity;
use Espo\ORM\Query\Select;

interface HookMediator
{
    
    public function beforeSave(Entity $entity, array $options): void;

    
    public function afterSave(Entity $entity, array $options): void;

    
    public function beforeRemove(Entity $entity, array $options): void;

    
    public function afterRemove(Entity $entity, array $options): void;

    
    public function beforeRelate(
        Entity $entity,
        string $relationName,
        Entity $foreignEntity,
        ?array $columnData,
        array $options
    ): void;

    
    public function afterRelate(
        Entity $entity,
        string $relationName,
        Entity $foreignEntity,
        ?array $columnData,
        array $options
    ): void;

    
    public function beforeUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void;

    
    public function afterUnrelate(Entity $entity, string $relationName, Entity $foreignEntity, array $options): void;

    
    public function beforeMassRelate(Entity $entity, string $relationName, Select $query, array $options): void;

    
    public function afterMassRelate(Entity $entity, string $relationName, Select $query, array $options): void;
}
