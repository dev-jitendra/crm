<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\BaseEntity;
use Espo\ORM\Query\Select;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Part\Join;
use Espo\ORM\Mapper\RDBMapper;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Repository\RDBRelationSelectBuilder as Builder;

use LogicException;
use RuntimeException;


class RDBRelation
{
    private EntityManager $entityManager;
    private HookMediator $hookMediator;
    private Entity $entity;
    private string $entityType;
    private ?string $foreignEntityType = null;
    private string $relationName;
    private ?string $relationType = null;
    private bool $noBuilder = false;

    public function __construct(
        EntityManager $entityManager,
        Entity $entity,
        string $relationName,
        HookMediator $hookMediator
    ) {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
        $this->hookMediator = $hookMediator;

        if (!$entity->hasId()) {
            throw new RuntimeException("Can't use an entity w/o ID.");
        }

        if (!$entity->hasRelation($relationName)) {
            throw new RuntimeException("Entity does not have a relation '$relationName'.");
        }

        $this->relationName = $relationName;
        $this->relationType = $entity->getRelationType($relationName);
        $this->entityType = $entity->getEntityType();

        if ($entity instanceof BaseEntity) {
            $this->foreignEntityType = $entity->getRelationParam($relationName, 'entity');
        }
        else {
            $this->foreignEntityType = $this->entityManager
                ->getDefs()
                ->getEntity($this->entityType)
                ->getRelation($relationName)
                ->getForeignEntityType();
        }

        if ($this->isBelongsToParentType()) {
            $this->noBuilder = true;
        }
    }

    
    private function createSelectBuilder(?Select $query = null): Builder
    {
        if ($this->noBuilder) {
            throw new RuntimeException("Can't use query builder for the '$this->relationType' relation type.");
        }

        return new Builder($this->entityManager, $this->entity, $this->relationName, $query);
    }

    
    public function clone(Select $query): Builder
    {
        if ($this->noBuilder) {
            throw new RuntimeException("Can't use clone for the '$this->relationType' relation type.");
        }

        if ($query->getFrom() !== $this->foreignEntityType) {
            throw new RuntimeException("Passed query doesn't match the entity type.");
        }

        return $this->createSelectBuilder($query);
    }

    private function isBelongsToParentType(): bool
    {
        return $this->relationType === Entity::BELONGS_TO_PARENT;
    }

    private function getMapper(): RDBMapper
    {
        $mapper = $this->entityManager->getMapper();

        
        if (!$mapper instanceof RDBMapper) {
            throw new LogicException();
        }

        return $mapper;
    }

    
    public function find(): Collection
    {
        if ($this->isBelongsToParentType()) {
            $collection = $this->entityManager->getCollectionFactory()->create();

            $entity = $this->getMapper()->selectRelated($this->entity, $this->relationName);

            if ($entity) {
                $collection[] = $entity;
            }

            $collection->setAsFetched();

            return $collection;
        }

        return $this->createSelectBuilder()->find();
    }

    
    public function findOne(): ?Entity
    {
        if ($this->isBelongsToParentType()) {
            $entity = $this->getMapper()->selectRelated($this->entity, $this->relationName);

            if ($entity && !$entity instanceof Entity) {
                throw new LogicException();
            }

            return $entity;
        }

        $collection = $this
            ->sth()
            ->limit(0, 1)
            ->find();

        foreach ($collection as $entity) {
            return $entity;
        }

        return null;
    }

    
    public function count(): int
    {
        return $this->createSelectBuilder()->count();
    }

    
    public function join($target, ?string $alias = null, $conditions = null): Builder
    {
        return $this->createSelectBuilder()->join($target, $alias, $conditions);
    }

    
    public function leftJoin($target, ?string $alias = null, $conditions = null): Builder
    {
        return $this->createSelectBuilder()->leftJoin($target, $alias, $conditions);
    }

    
    public function distinct(): Builder
    {
        return $this->createSelectBuilder()->distinct();
    }

    
    public function sth(): Builder
    {
        return $this->createSelectBuilder()->sth();
    }

    
    public function where($clause = [], $value = null): Builder
    {
        return $this->createSelectBuilder()->where($clause, $value);
    }

    
    public function having($clause = [], $value = null): Builder
    {
        return $this->createSelectBuilder()->having($clause, $value);
    }

    
    public function order($orderBy = 'id', $direction = null): Builder
    {
        return $this->createSelectBuilder()->order($orderBy, $direction);
    }

    
    public function limit(?int $offset = null, ?int $limit = null): Builder
    {
        return $this->createSelectBuilder()->limit($offset, $limit);
    }

    
    public function select($select = [], ?string $alias = null): Builder
    {
        return $this->createSelectBuilder()->select($select, $alias);
    }

    
    public function group($groupBy): Builder
    {
        return $this->createSelectBuilder()->group($groupBy);
    }

    
    public function groupBy($groupBy): Builder
    {
        return $this->group($groupBy);
    }

    
    public function columnsWhere($clause): Builder
    {
        return $this->createSelectBuilder()->columnsWhere($clause);
    }

    private function processCheckForeignEntity(Entity $entity): void
    {
        if ($this->foreignEntityType && $this->foreignEntityType !== $entity->getEntityType()) {
            throw new RuntimeException("Entity type doesn't match an entity type of the relation.");
        }

        if (!$entity->hasId()) {
            throw new RuntimeException("Can't use an entity w/o ID.");
        }
    }

    
    public function isRelated(Entity $entity): bool
    {
        if (!$entity->hasId()) {
            throw new RuntimeException("Can't use an entity w/o ID.");
        }

        if ($this->isBelongsToParentType()) {
            return $this->isRelatedBelongsToParent($entity);
        }

        if ($this->relationType === Entity::BELONGS_TO) {
            return $this->isRelatedBelongsTo($entity);
        }

        $this->processCheckForeignEntity($entity);

        return (bool) $this->createSelectBuilder()
            ->select(['id'])
            ->where(['id' => $entity->getId()])
            ->findOne();
    }

    
    public function isRelatedById(string $id): bool
    {
        if ($this->isBelongsToParentType()) {
            throw new LogicException("Can't use isRelatedById for 'belongsToParent'.");
        }

        return (bool) $this->createSelectBuilder()
            ->select(['id'])
            ->where(['id' => $id])
            ->findOne();
    }

    private function isRelatedBelongsToParent(Entity $entity): bool
    {
        $fromEntity = $this->entity;

        $idAttribute = $this->relationName . 'Id';
        $typeAttribute = $this->relationName . 'Type';

        if (!$fromEntity->has($idAttribute) || !$fromEntity->has($typeAttribute)) {
            $fromEntity = $this->entityManager->getEntity($fromEntity->getEntityType(), $fromEntity->getId());
        }

        if (!$fromEntity) {
            return false;
        }

        return
            $fromEntity->get($idAttribute) === $entity->getId() &&
            $fromEntity->get($typeAttribute) === $entity->getEntityType();
    }

    private function isRelatedBelongsTo(Entity $entity): bool
    {
        $fromEntity = $this->entity;

        $idAttribute = $this->relationName . 'Id';

        if (!$fromEntity->has($idAttribute)) {
            $fromEntity = $this->entityManager->getEntity($fromEntity->getEntityType(), $fromEntity->getId());
        }

        if (!$fromEntity) {
            return false;
        }

        return $fromEntity->get($idAttribute) === $entity->getId();
    }

    
    public function relateById(string $id, ?array $columnData = null, array $options = []): void
    {
        if ($this->isBelongsToParentType()) {
            throw new RuntimeException("Can't relate 'belongToParent'.");
        }

        if ($id === '') {
            throw new RuntimeException();
        }

        
        $foreignEntityType = $this->foreignEntityType;

        $seed = $this->entityManager->getEntityFactory()->create($foreignEntityType);

        $seed->set('id', $id);

        $this->relate($seed, $columnData, $options);
    }

    
    public function unrelateById(string $id, array $options = []): void
    {
        if ($this->isBelongsToParentType()) {
            throw new RuntimeException("Can't unrelate 'belongToParent'.");
        }

        if ($id === '') {
            throw new RuntimeException();
        }

        
        $foreignEntityType = $this->foreignEntityType;

        $seed = $this->entityManager->getEntityFactory()->create($foreignEntityType);

        $seed->set('id', $id);

        $this->unrelate($seed, $options);
    }

    
    public function updateColumnsById(string $id, array $columnData): void
    {
        if ($this->isBelongsToParentType()) {
            throw new RuntimeException("Can't update columns by ID 'belongToParent'.");
        }

        if ($id === '') {
            throw new RuntimeException();
        }

        
        $foreignEntityType = $this->foreignEntityType;

        $seed = $this->entityManager->getEntityFactory()->create($foreignEntityType);

        $seed->set('id', $id);

        $this->updateColumns($seed, $columnData);
    }

    
    public function relate(Entity $entity, ?array $columnData = null, array $options = []): void
    {
        $this->processCheckForeignEntity($entity);
        $this->beforeRelate($entity, $columnData, $options);

        $result = $this->getMapper()->relate($this->entity, $this->relationName, $entity, $columnData);

        if (!$result) {
            return;
        }

        $this->afterRelate($entity, $columnData, $options);
    }

    
    public function unrelate(Entity $entity, array $options = []): void
    {
        $this->processCheckForeignEntity($entity);
        $this->beforeUnrelate($entity, $options);
        $this->getMapper()->unrelate($this->entity, $this->relationName, $entity);
        $this->afterUnrelate($entity, $options);
    }

    
    public function massRelate(Select $query, array $options = []): void
    {
        if ($this->isBelongsToParentType()) {
            throw new RuntimeException("Can't mass relate 'belongToParent'.");
        }

        if ($query->getFrom() !== $this->foreignEntityType) {
            throw new RuntimeException("Passed query doesn't match foreign entity type.");
        }

        $this->beforeMassRelate($query, $options);
        $this->getMapper()->massRelate($this->entity, $this->relationName, $query);
        $this->afterMassRelate($query, $options);
    }

    
    public function updateColumns(Entity $entity, array $columnData): void
    {
        $this->processCheckForeignEntity($entity);

        if ($this->relationType !== Entity::MANY_MANY) {
            throw new RuntimeException("Can't update not many-to-many relation.");
        }

        if (!$entity->hasId()) {
            throw new RuntimeException("Entity w/o ID.");
        }

        $id = $entity->getId();

        $this->getMapper()->updateRelationColumns($this->entity, $this->relationName, $id, $columnData);
    }

    
    public function getColumn(Entity $entity, string $column)
    {
        $this->processCheckForeignEntity($entity);

        if ($this->relationType !== Entity::MANY_MANY) {
            throw new RuntimeException("Can't get a column of not many-to-many relation.");
        }

        if (!$entity->hasId()) {
            throw new RuntimeException("Entity w/o ID.");
        }

        $id = $entity->getId();

        return $this->getMapper()->getRelationColumn($this->entity, $this->relationName, $id, $column);
    }

    
    public function getColumnById(string $id, string $column): string|int|float|bool|null
    {
        if ($this->relationType !== Entity::MANY_MANY) {
            throw new RuntimeException("Can't get a column of not many-to-many relation.");
        }

        return $this->getMapper()->getRelationColumn($this->entity, $this->relationName, $id, $column);
    }

    
    private function beforeRelate(Entity $entity, ?array $columnData, array $options): void
    {
        $this->hookMediator->beforeRelate($this->entity, $this->relationName, $entity, $columnData, $options);
    }

    
    private function afterRelate(Entity $entity, ?array $columnData, array $options): void
    {
        $this->hookMediator->afterRelate($this->entity, $this->relationName, $entity, $columnData, $options);
    }

    
    private function beforeUnrelate(Entity $entity, array $options): void
    {
        $this->hookMediator->beforeUnrelate($this->entity, $this->relationName, $entity, $options);
    }

    
    private function afterUnrelate(Entity $entity, array $options): void
    {
        $this->hookMediator->afterUnrelate($this->entity, $this->relationName, $entity, $options);
    }

    
    private function beforeMassRelate(Select $query, array $options): void
    {
        $this->hookMediator->beforeMassRelate($this->entity, $this->relationName, $query, $options);
    }

    
    private function afterMassRelate(Select $query, array $options): void
    {
        $this->hookMediator->afterMassRelate($this->entity, $this->relationName, $query, $options);
    }
}
