<?php


namespace Espo\Core\Duplicate;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Select\SelectBuilderFactory;

use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\Part\WhereItem;
use RuntimeException;

class Finder
{
    private const LIMIT = 10;

    
    private array $whereBuilderMap = [];

    public function __construct(
        private EntityManager $entityManager,
        private SelectBuilderFactory $selectBuilderFactory,
        private WhereBuilderFactory $whereBuilderFactory
    ) {}

    
    public function check(Entity $entity): bool
    {
        $where = $this->getWhere($entity);

        if (!$where) {
            return false;
        }

        return $this->checkByWhere($entity, $where);
    }

    
    public function find(Entity $entity): ?Collection
    {
        $where = $this->getWhere($entity);

        if (!$where) {
            return null;
        }

        return $this->findByWhere($entity, $where);
    }

    
    public function checkByWhere(Entity $entity, WhereItem $where): bool
    {
        $entityType = $entity->getEntityType();

        if ($entity->hasId()) {
            $where = Cond::and(
                $where,
                Cond::notEqual(
                    Cond::column('id'),
                    $entity->getId()
                )
            );
        }

        $duplicate = $this->entityManager
            ->getRDBRepository($entityType)
            ->where($where)
            ->select('id')
            ->findOne();

        return (bool) $duplicate;
    }

    
    public function findByWhere(Entity $entity, WhereItem $where): ?Collection
    {
        $entityType = $entity->getEntityType();

        if ($entity->hasId()) {
            $where = Cond::and(
                $where,
                Cond::notEqual(
                    Cond::column('id'),
                    $entity->getId()
                )
            );
        }

        try {
            $query = $this->selectBuilderFactory
                ->create()
                ->from($entityType)
                ->withStrictAccessControl()
                ->buildQueryBuilder()
                ->where($where)
                ->select(['id'])
                ->limit(0, self::LIMIT)
                ->build();
        }
        catch (Error|Forbidden|BadRequest $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }

        $builder = $this->entityManager
            ->getRDBRepository($entityType)
            ->clone($query);

        if (!$builder->findOne()) {
            return null;
        }

        return $builder->select(['*'])->find();
    }

    private function getWhere(Entity $entity): ?WhereItem
    {
        $entityType = $entity->getEntityType();

        if (!array_key_exists($entityType, $this->whereBuilderMap)) {
            $this->whereBuilderMap[$entityType] = $this->loadWhereBuilder($entityType);
        }

        $builder = $this->whereBuilderMap[$entityType];

        return $builder?->build($entity);
    }

    
    private function loadWhereBuilder(string $entityType): ?WhereBuilder
    {
        if (!$this->whereBuilderFactory->has($entityType)) {
            return null;
        }

        return $this->whereBuilderFactory->create($entityType);
    }
}
