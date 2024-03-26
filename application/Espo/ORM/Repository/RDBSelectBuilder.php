<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Collection;
use Espo\ORM\EntityCollection;
use Espo\ORM\SthCollection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Select;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Part\Join;
use Espo\ORM\Mapper\Mapper;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Mapper\BaseMapper;

use RuntimeException;


class RDBSelectBuilder
{
    private SelectBuilder $builder;
    
    private RDBRepository $repository;

    private bool $returnSthCollection = false;

    public function __construct(
        private EntityManager $entityManager,
        string $entityType,
        ?Select $query = null
    ) {

        
        $repository = $this->entityManager->getRepository($entityType);

        $this->repository = $repository;

        if ($query && $query->getFrom() !== $entityType) {
            throw new RuntimeException("SelectBuilder: Passed query doesn't match the entity type.");
        }

        $this->builder = new SelectBuilder();

        if ($query) {
            $this->builder->clone($query);
        }

        if (!$query) {
            $this->builder->from($entityType);
        }
    }

    protected function getMapper(): Mapper
    {
        return $this->entityManager->getMapper();
    }

    
    public function find(?array $params = null): Collection
    {
        $query = $this->getMergedParams($params);

        
        $collection = $this->getMapper()->select($query);

        return $this->handleReturnCollection($collection);
    }

    
    public function findOne(?array $params = null): ?Entity
    {
        $builder = $this;

        if ($params !== null) { 
            $query = $this->getMergedParams($params);

            $builder = $this->repository->clone($query);
        }

        $collection = $builder->sth()->limit(0, 1)->find();

        foreach ($collection as $entity) {
            return $entity;
        }

        return null;
    }

    
    public function count(?array $params = null): int
    {
        if ($params) { 
            $query = $this->getMergedParams($params);
            return $this->getMapper()->count($query);
        }

        $query = $this->builder->build();

        return $this->getMapper()->count($query);
    }

    
    public function max(string $attribute)
    {
        $query = $this->builder->build();

        $mapper = $this->getMapper();

        if (!$mapper instanceof BaseMapper) {
            throw new RuntimeException("Not supported 'max'.");
        }

        return $mapper->max($query, $attribute);
    }

    
    public function min(string $attribute)
    {
        $query = $this->builder->build();

        $mapper = $this->getMapper();

        if (!$mapper instanceof BaseMapper) {
            throw new RuntimeException("Not supported 'min'.");
        }

        return $mapper->min($query, $attribute);
    }

    
    public function sum(string $attribute)
    {
        $query = $this->builder->build();

        $mapper = $this->getMapper();

        if (!$mapper instanceof BaseMapper) {
            throw new RuntimeException("Not supported 'sum'.");
        }

        return $mapper->sum($query, $attribute);
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

    
    public function forUpdate(): self
    {
        $this->builder->forUpdate();
        $this->sth();

        return $this;
    }

    
    public function sth(): self
    {
        $this->returnSthCollection = true;

        return $this;
    }

    
    public function where($clause = [], $value = null): self
    {
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

    
    protected function handleReturnCollection(Collection $collection): Collection
    {
        if (!$collection instanceof SthCollection) {
            return $collection;
        }

        if ($this->returnSthCollection) {
            return $collection;
        }

        
        return $this->entityManager->getCollectionFactory()->createFromSthCollection($collection);
    }

    
    protected function getMergedParams(?array $params = null): Select
    {
        if ($params === null || empty($params)) {
            return $this->builder->build();
        }

        $builtParams = $this->builder->build()->getRaw();

        $whereClause = $builtParams['whereClause'] ?? [];
        $havingClause = $builtParams['havingClause'] ?? [];
        $joins = $builtParams['joins'] ?? [];
        $leftJoins = $builtParams['leftJoins'] ?? [];

        if (!empty($params['whereClause'])) {
            unset($builtParams['whereClause']);
            if (count($whereClause)) {
                $params['whereClause'][] = $whereClause;
            }
        }

        if (!empty($params['havingClause'])) {
            unset($builtParams['havingClause']);
            if (count($havingClause)) {
                $params['havingClause'][] = $havingClause;
            }
        }

        if (empty($params['whereClause'])) {
            unset($params['whereClause']);
        }

        if (empty($params['havingClause'])) {
            unset($params['havingClause']);
        }

        if (!empty($params['leftJoins']) && !empty($leftJoins)) {
            foreach ($leftJoins as $j) {
                $params['leftJoins'][] = $j;
            }
        }

        if (!empty($params['joins']) && !empty($joins)) {
            foreach ($joins as $j) {
                $params['joins'][] = $j;
            }
        }

        $params = array_replace_recursive($builtParams, $params);

        return Select::fromRaw($params);
    }
}
