<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Collection;
use Espo\ORM\Mapper\RDBMapper;
use Espo\ORM\SthCollection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\BaseEntity;
use Espo\ORM\Query\Select;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Part\Join;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Order;

use LogicException;
use RuntimeException;
use InvalidArgumentException;


class RDBRelationSelectBuilder
{
    private EntityManager $entityManager;
    private Entity $entity;
    private string $foreignEntityType;
    private string $relationName;
    private ?string $relationType;
    private SelectBuilder $builder;
    private ?string $middleTableAlias = null;
    private bool $returnSthCollection = false;

    public function __construct(
        EntityManager $entityManager,
        Entity $entity,
        string $relationName,
        ?Select $query = null
    ) {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
        $this->relationName = $relationName;
        $this->relationType = $entity->getRelationType($relationName);
        $entityType = $entity->getEntityType();

        if ($entity instanceof BaseEntity) {
            $this->foreignEntityType = $entity->getRelationParam($relationName, 'entity');
        }
        else {
            $this->foreignEntityType = $this->entityManager
                ->getDefs()
                ->getEntity($entityType)
                ->getRelation($relationName)
                ->getForeignEntityType();
        }

        $this->builder = $query ?
            $this->cloneQueryToBuilder($query) :
            $this->createSelectBuilder()->from($this->foreignEntityType);
    }

    private function cloneQueryToBuilder(Select $query): SelectBuilder
    {
        $where = $query->getWhere();

        if ($where === null) {
            return $this->createSelectBuilder()->clone($query);
        }

        $rawQuery = $query->getRaw();

        $rawQuery['whereClause'] = $this->applyRelationAliasToWhereClause($where->getRaw());

        $newQuery = Select::fromRaw($rawQuery);

        return $this->createSelectBuilder()->clone($newQuery);
    }

    private function createSelectBuilder(): SelectBuilder
    {
        return new SelectBuilder();
    }

    private function getMapper(): RDBMapper
    {
        $mapper = $this->entityManager->getMapper();

        
        if (!$mapper instanceof RDBMapper) {
            throw new LogicException();
        }

        return $mapper;
    }

    
    public function columnsWhere($clause): self
    {
        if ($this->relationType !== Entity::MANY_MANY) {
            throw new RuntimeException("Can't add columns where for not many-to-many relationship.");
        }

        if ($clause instanceof WhereItem) {
            $clause = $clause->getRaw();
        }

        if (!is_array($clause)) {
            throw new InvalidArgumentException();
        }

        $transformedWhere = $this->applyMiddleAliasToWhere($clause);

        $this->where($transformedWhere);

        return $this;
    }

    
    private function applyMiddleAliasToWhere(array $where): array
    {
        $transformedWhere = [];

        $middleName = lcfirst($this->getRelationParam('relationName'));

        foreach ($where as $key => $value) {
            $transformedKey = $key;
            $transformedValue = $value;

            if (
                is_string($key) &&
                strlen($key) &&
                !str_contains($key, '.') &&
                $key[0] === strtolower($key[0])
            ) {
                $transformedKey = $middleName . '.' . $key;
            }

            if (is_array($value)) {
                $transformedValue = $this->applyMiddleAliasToWhere($value);
            }

            $transformedWhere[$transformedKey] = $transformedValue;
        }

        return $transformedWhere;
    }

    
    public function find(): Collection
    {
        $query = $this->builder->build();

        $related = $this->getMapper()->selectRelated($this->entity, $this->relationName, $query);

        if ($related instanceof Collection) {
            return $this->handleReturnCollection($related);
        }

        $collection = $this->entityManager->getCollectionFactory()->create($this->foreignEntityType);

        $collection->setAsFetched();

        if ($related instanceof Entity) {
            $collection[] = $related;
        }

        return $collection;
    }

    
    public function findOne(): ?Entity
    {
        $collection = $this->sth()->limit(0, 1)->find();

        foreach ($collection as $entity) {
            return $entity;
        }

        return null;
    }

    
    public function count(): int
    {
        $query = $this->builder->build();

        return $this->getMapper()->countRelated($this->entity, $this->relationName, $query);
    }

    
    public function join($target, ?string $alias = null, $conditions = null): self
    {
        $this->builder->join($target, $alias, $conditions);

        return $this;
    }

    
    public function leftJoin($target, ?string $alias = null, $conditions = null): self
    {
        $this->builder->leftJoin($target, $alias, $conditions);

        return $this;
    }

    
    public function distinct(): self
    {
        $this->builder->distinct();

        return $this;
    }

    
    public function sth(): self
    {
        $this->returnSthCollection = true;

        return $this;
    }

    
    public function where($clause = [], $value = null): self
    {
        if ($this->isManyMany()) {
            if ($clause instanceof WhereItem) {
                $clause = $this->applyRelationAliasToWhereClause($clause->getRaw());
            }
            else if (is_string($clause)) {
                $clause = $this->applyRelationAliasToWhereClauseKey($clause);
            }
            else if (is_array($clause)) {
                $clause = $this->applyRelationAliasToWhereClause($clause);
            }
        }

        $this->builder->where($clause, $value);

        return $this;
    }

    
    public function having($clause = [], $value = null): self
    {
        $this->builder->having($clause, $value);

        return $this;
    }

    
    public function order($orderBy = 'id', $direction = null): self
    {
        $this->builder->order($orderBy, $direction);

        return $this;
    }

    
    public function limit(?int $offset = null, ?int $limit = null): self
    {
        $this->builder->limit($offset, $limit);

        return $this;
    }

    
    public function select($select, ?string $alias = null): self
    {
        $this->builder->select($select, $alias);

        return $this;
    }

    
    public function group($groupBy): self
    {
        $this->builder->group($groupBy);

        return $this;
    }

    
    public function groupBy($groupBy): self
    {
        return $this->group($groupBy);
    }

    private function getMiddleTableAlias(): ?string
    {
        if (!$this->isManyMany()) {
            return null;
        }

        if (!$this->middleTableAlias) {
            $middleName = $this->getRelationParam('relationName');

            if (!$middleName) {
                throw new RuntimeException("No relation name.");
            }

            $this->middleTableAlias = lcfirst($middleName);
        }

        return $this->middleTableAlias;
    }

    private function applyRelationAliasToWhereClauseKey(string $item): string
    {
        if (!$this->isManyMany()) {
            return $item;
        }

        $alias = $this->getMiddleTableAlias();

        return str_replace('@relation.', $alias . '.', $item);
    }

    
    private function applyRelationAliasToWhereClause(array $where): array
    {
        if (!$this->isManyMany()) {
            return $where;
        }

        $transformedWhere = [];

        foreach ($where as $key => $value) {
            $transformedKey = $key;
            $transformedValue = $value;

            if (is_string($key)) {
                $transformedKey = $this->applyRelationAliasToWhereClauseKey($key);
            }

            if (is_array($value)) {
                $transformedValue = $this->applyRelationAliasToWhereClause($value);
            }

            $transformedWhere[$transformedKey] = $transformedValue;
        }

        return $transformedWhere;
    }

    private function isManyMany(): bool
    {
        return $this->relationType === Entity::MANY_MANY;
    }

    
    private function handleReturnCollection(Collection $collection): Collection
    {
        if (!$collection instanceof SthCollection) {
            return $collection;
        }

        if ($this->returnSthCollection) {
            return $collection;
        }

        return $this->entityManager->getCollectionFactory()->createFromSthCollection($collection);
    }

    
    private function getRelationParam(string $param)
    {
        if ($this->entity instanceof BaseEntity) {
            return $this->entity->getRelationParam($this->relationName, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($this->entity->getEntityType());

        if (!$entityDefs->hasRelation($this->relationName)) {
            return null;
        }

        return $entityDefs->getRelation($this->relationName)->getParam($param);
    }
}
