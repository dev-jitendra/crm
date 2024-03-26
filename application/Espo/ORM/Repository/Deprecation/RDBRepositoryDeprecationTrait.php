<?php


namespace Espo\ORM\Repository\Deprecation;

use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Mapper\BaseMapper;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Select;
use Espo\ORM\Repository\RDBSelectBuilder;
use Espo\ORM\SthCollection;


trait RDBRepositoryDeprecationTrait
{
    
    public function groupBy($groupBy): RDBSelectBuilder
    {
        return $this->group($groupBy);
    }

    
    protected function getPDO(): \PDO
    {
        return $this->entityManager->getPDO();
    }

    
    protected function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    
    public function deleteFromDb(string $id, bool $onlyDeleted = false): void
    {
        $mapper = $this->getMapper();

        if (!$mapper instanceof BaseMapper) {
            throw new \RuntimeException("Not supported 'deleteFromDb'.");
        }

        $mapper->deleteFromDb($this->entityType, $id, $onlyDeleted);
    }

    
    public function get(?string $id = null): ?Entity
    {
        if (is_null($id)) {
            return $this->getNew();
        }

        return $this->getById($id);
    }

    
    public function findRelated(Entity $entity, string $relationName, ?array $params = null)
    {
        $params = $params ?? [];

        if ($entity->getEntityType() !== $this->entityType) {
            throw new \RuntimeException("Not supported entity type.");
        }

        if (!$entity->hasId()) {
            return null;
        }

        $type = $entity->getRelationType($relationName);
        
        $entityType = $entity->getRelationParam($relationName, 'entity');

        $additionalColumns = $params['additionalColumns'] ?? [];
        unset($params['additionalColumns']);

        $additionalColumnsConditions = $params['additionalColumnsConditions'] ?? [];
        unset($params['additionalColumnsConditions']);

        $select = null;

        if ($entityType) {
            $params['from'] = $entityType;
            $select = Select::fromRaw($params);
        }

        if ($type === Entity::MANY_MANY && count($additionalColumns)) {
            if ($select === null) {
                throw new \RuntimeException();
            }

            $select = $this->applyRelationAdditionalColumns($entity, $relationName, $additionalColumns, $select);
        }

        
        if ($type === Entity::MANY_MANY && count($additionalColumnsConditions)) {
            if ($select === null) {
                throw new \RuntimeException();
            }

            $select = $this->applyRelationAdditionalColumnsConditions(
                $entity,
                $relationName,
                $additionalColumnsConditions,
                $select
            );
        }

        
        $result = $this->getMapper()->selectRelated($entity, $relationName, $select);

        if ($result instanceof SthCollection) {
            
            return $this->entityManager->getCollectionFactory()->createFromSthCollection($result);
        }

        return $result;
    }

    
    public function countRelated(Entity $entity, string $relationName, ?array $params = null): int
    {
        $params = $params ?? [];

        if ($entity->getEntityType() !== $this->entityType) {
            throw new \RuntimeException("Not supported entity type.");
        }

        if (!$entity->hasId()) {
            return 0;
        }

        $type = $entity->getRelationType($relationName);
        
        $entityType = $entity->getRelationParam($relationName, 'entity');

        $additionalColumnsConditions = $params['additionalColumnsConditions'] ?? [];
        unset($params['additionalColumnsConditions']);

        $select = null;

        if ($entityType) {
            $params['from'] = $entityType;

            $select = Select::fromRaw($params);
        }

        if ($type === Entity::MANY_MANY && count($additionalColumnsConditions)) {
            if ($select === null) {
                throw new \RuntimeException();
            }

            $select = $this->applyRelationAdditionalColumnsConditions(
                $entity,
                $relationName,
                $additionalColumnsConditions,
                $select
            );
        }

        return (int) $this->getMapper()->countRelated($entity, $relationName, $select);
    }

    
    private function applyRelationAdditionalColumns(
        Entity $entity,
        string $relationName,
        array $columns,
        Select $select
    ): Select {

        if (empty($columns)) {
            return $select;
        }

        
        $middleName = lcfirst($entity->getRelationParam($relationName, 'relationName'));

        $selectItemList = $select->getSelect();

        if ($selectItemList === []) {
            $selectItemList[] = '*';
        }

        foreach ($columns as $column => $alias) {
            $selectItemList[] = [
                $middleName . '.' . $column,
                $alias
            ];
        }

        return $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->clone($select)
            ->select($selectItemList)
            ->build();
    }

    
    private function applyRelationAdditionalColumnsConditions(
        Entity $entity,
        string $relationName,
        array $conditions,
        Select $select
    ): Select {

        if (empty($conditions)) {
            return $select;
        }

        
        $middleName = lcfirst($entity->getRelationParam($relationName, 'relationName'));

        $builder = $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->clone($select);

        foreach ($conditions as $column => $value) {
            $builder->where(
                $middleName . '.' . $column,
                $value
            );
        }

        return $builder->build();
    }
    
    public function isRelated(Entity $entity, string $relationName, $foreign): bool
    {
        if (!$entity->hasId()) {
            return false;
        }

        if ($entity->getEntityType() !== $this->entityType) {
            throw new \RuntimeException("Not supported entity type.");
        }

        

        if ($foreign instanceof Entity) {
            if (!$foreign->hasId()) {
                return false;
            }

            $id = $foreign->getId();
        }
        else if (is_string($foreign)) {
            $id = $foreign;
        }
        else {
            throw new \RuntimeException("Bad 'foreign' value.");
        }

        if (!$id) {
            return false;
        }

        if (in_array($entity->getRelationType($relationName), [Entity::BELONGS_TO, Entity::BELONGS_TO_PARENT])) {
            if (!$entity->has($relationName . 'Id')) {
                $entity = $this->getById($entity->getId());
            }
        }

        

        $relation = $this->getRelation($entity, $relationName);

        if ($foreign instanceof Entity) {
            return $relation->isRelated($foreign);
        }

        return (bool) $this->countRelated($entity, $relationName, [
            'whereClause' => [
                'id' => $id,
            ],
        ]);
    }
    
    public function relate(Entity $entity, string $relationName, $foreign, $columnData = null, array $options = [])
    {
        if (!$entity->hasId()) {
            throw new \RuntimeException("Can't relate an entity w/o ID.");
        }

        if (!$foreign instanceof Entity && !is_string($foreign)) {
            throw new \RuntimeException("Bad 'foreign' value.");
        }

        if ($entity->getEntityType() !== $this->entityType) {
            throw new \RuntimeException("Not supported entity type.");
        }

        $this->beforeRelate($entity, $relationName, $foreign, $columnData, $options);

        $beforeMethodName = 'beforeRelate' . ucfirst($relationName);

        if (method_exists($this, $beforeMethodName)) {
            $this->$beforeMethodName($entity, $foreign, $columnData, $options);
        }

        $result = false;

        $methodName = 'relate' . ucfirst($relationName);

        if (method_exists($this, $methodName)) {
            $result = $this->$methodName($entity, $foreign, $columnData, $options);
        }
        else {
            $data = $columnData;

            if ($columnData instanceof \stdClass) {
                $data = get_object_vars($columnData);
            }

            if ($foreign instanceof Entity) {
                $result = $this->getMapper()->relate($entity, $relationName, $foreign, $data);
            }
            else {
                $id = $foreign;

                $result = $this->getMapper()->relateById($entity, $relationName, $id, $data);
            }
        }

        if ($result) {
            $this->afterRelate($entity, $relationName, $foreign, $columnData, $options);

            $afterMethodName = 'afterRelate' . ucfirst($relationName);

            if (method_exists($this, $afterMethodName)) {
                $this->$afterMethodName($entity, $foreign, $columnData, $options);
            }
        }

        return $result;
    }

    
    public function unrelate(Entity $entity, string $relationName, $foreign, array $options = [])
    {
        if (!$entity->hasId()) {
            throw new \RuntimeException("Can't unrelate an entity w/o ID.");
        }

        if (!$foreign instanceof Entity && !is_string($foreign)) {
            throw new \RuntimeException("Bad foreign value.");
        }

        if ($entity->getEntityType() !== $this->entityType) {
            throw new \RuntimeException("Not supported entity type.");
        }

        $this->beforeUnrelate($entity, $relationName, $foreign, $options);

        $beforeMethodName = 'beforeUnrelate' . ucfirst($relationName);

        if (method_exists($this, $beforeMethodName)) {
            $this->$beforeMethodName($entity, $foreign, $options);
        }

        $result = false;

        $methodName = 'unrelate' . ucfirst($relationName);

        if (method_exists($this, $methodName)) {
            $this->$methodName($entity, $foreign);
        }
        else {
            if ($foreign instanceof Entity) {
                $this->getMapper()->unrelate($entity, $relationName, $foreign);
            }
            else {
                $id = $foreign;

                $this->getMapper()->unrelateById($entity, $relationName, $id);
            }
        }

        $this->afterUnrelate($entity, $relationName, $foreign, $options);

        $afterMethodName = 'afterUnrelate' . ucfirst($relationName);

        if (method_exists($this, $afterMethodName)) {
            $this->$afterMethodName($entity, $foreign, $options);
        }

        return $result;
    }

    
    public function getRelationColumn(Entity $entity, string $relationName, string $foreignId, string $column)
    {
        return $this->getMapper()->getRelationColumn($entity, $relationName, $foreignId, $column);
    }

    
    public function updateRelation(Entity $entity, string $relationName, $foreign, $columnData)
    {
        if (!$entity->hasId()) {
            throw new \RuntimeException("Can't update a relation for an entity w/o ID.");
        }

        if (!$foreign instanceof Entity && !is_string($foreign)) {
            throw new \RuntimeException("Bad foreign value.");
        }

        if ($columnData instanceof \stdClass) {
            $columnData = get_object_vars($columnData);
        }

        if ($foreign instanceof Entity) {
            $id = $foreign->getId();
        } else {
            $id = $foreign;
        }

        if (!is_string($id)) {
            throw new \RuntimeException("Bad foreign value.");
        }

        $this->getMapper()->updateRelationColumns($entity, $relationName, $id, $columnData);

        return true;
    }

    
    public function massRelate(Entity $entity, string $relationName, array $params = [], array $options = [])
    {
        if (!$entity->hasId()) {
            throw new \RuntimeException("Can't related an entity w/o ID.");
        }

        $this->beforeMassRelate($entity, $relationName, $params, $options);

        $select = Select::fromRaw($params);

        $this->getMapper()->massRelate($entity, $relationName, $select);

        $this->afterMassRelate($entity, $relationName, $params, $options);
    }
}
