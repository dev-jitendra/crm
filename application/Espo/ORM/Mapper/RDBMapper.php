<?php


namespace Espo\ORM\Mapper;

use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\Query\Select;

interface RDBMapper extends Mapper
{
    
    public function relate(Entity $entity, string $relationName, Entity $foreignEntity, ?array $columnData): bool;

    
    public function unrelate(Entity $entity, string $relationName, Entity $foreignEntity): void;

    
    public function relateById(Entity $entity, string $relationName, string $id, ?array $columnData = null): bool;

    
    public function unrelateById(Entity $entity, string $relationName, string $id): void;

    
    public function massRelate(Entity $entity, string $relationName, Select $select): void;

    
    public function updateRelationColumns(
        Entity $entity,
        string $relationName,
        string $id,
        array $columnData
    ): void;

    
    public function getRelationColumn(
        Entity $entity,
        string $relationName,
        string $id,
        string $column
    ): string|int|float|bool|null;

    
    public function selectRelated(Entity $entity, string $relationName, ?Select $select = null): Collection|Entity|null;

    
    public function countRelated(Entity $entity, string $relationName, ?Select $select = null): int;
}
