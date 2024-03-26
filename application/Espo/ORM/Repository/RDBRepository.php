<?php


namespace Espo\ORM\Repository;

use Espo\ORM\EntityManager;
use Espo\ORM\EntityFactory;
use Espo\ORM\Collection;
use Espo\ORM\Repository\Deprecation\RDBRepositoryDeprecationTrait;
use Espo\ORM\Repository\Option\SaveOption;
use Espo\ORM\SthCollection;
use Espo\ORM\BaseEntity;
use Espo\ORM\Entity;
use Espo\ORM\Mapper\RDBMapper;
use Espo\ORM\Query\Select;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Query\Part\Selection;
use Espo\ORM\Query\Part\Join;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Mapper\BaseMapper;

use RuntimeException;


class RDBRepository implements Repository
{
    
    use RDBRepositoryDeprecationTrait;

    protected HookMediator $hookMediator;
    protected RDBTransactionManager $transactionManager;

    public function __construct(
        protected string $entityType,
        protected EntityManager $entityManager,
        protected EntityFactory $entityFactory,
        ?HookMediator $hookMediator = null
    ) {
        $this->hookMediator = $hookMediator ?? (new EmptyHookMediator());
        $this->transactionManager = new RDBTransactionManager($entityManager->getTransactionManager());
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    
    public function getNew(): Entity
    {
        $entity = $this->entityFactory->create($this->entityType);

        if ($entity instanceof BaseEntity) {
            $entity->populateDefaults();
        }

        
        return $entity;
    }

    
    public function getById(string $id): ?Entity
    {
        $selectQuery = $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->from($this->entityType)
            ->where([
                'id' => $id,
            ])
            ->build();

        
        $entity = $this->getMapper()->selectOne($selectQuery);

        return $entity;
    }

    protected function processCheckEntity(Entity $entity): void
    {
        if ($entity->getEntityType() !== $this->entityType) {
            throw new RuntimeException("An entity type doesn't match the repository.");
        }
    }

    
    public function save(Entity $entity, array $options = []): void
    {
        $this->processCheckEntity($entity);

        if ($entity instanceof BaseEntity) {
            $entity->setAsBeingSaved();
        }

        if (empty($options['skipBeforeSave']) && empty($options[SaveOption::SKIP_ALL])) {
            $this->beforeSave($entity, $options);
        }

        $isSaved = false;

        if ($entity instanceof BaseEntity) {
            $isSaved = $entity->isSaved();
        }

        if ($entity->isNew() && !$isSaved) {
            $this->getMapper()->insert($entity);
        }
        else {
            $this->getMapper()->update($entity);
        }

        if ($entity instanceof BaseEntity) {
            $entity->setAsSaved();
        }

        if (
            empty($options['skipAfterSave']) &&
            empty($options[SaveOption::SKIP_ALL])
        ) {
            $this->afterSave($entity, $options);
        }

        if ($entity->isNew()) {
            if (empty($options[SaveOption::KEEP_NEW])) {
                $entity->setAsNotNew();

                $entity->updateFetchedValues();
            }
        }
        else {
            if (empty($options[SaveOption::KEEP_DIRTY])) {
                $entity->updateFetchedValues();
            }
        }

        if ($entity instanceof BaseEntity) {
            $entity->setAsNotBeingSaved();
        }
    }

    
    public function restoreDeleted(string $id): void
    {
        $mapper = $this->getMapper();

        if (!$mapper instanceof BaseMapper) {
            throw new RuntimeException("Not supported 'restoreDeleted'.");
        }

        $mapper->restoreDeleted($this->entityType, $id);
    }

    
    public function getRelation(Entity $entity, string $relationName): RDBRelation
    {
        return new RDBRelation($this->entityManager, $entity, $relationName, $this->hookMediator);
    }

    
    public function remove(Entity $entity, array $options = []): void
    {
        $this->processCheckEntity($entity);
        $this->beforeRemove($entity, $options);
        $this->getMapper()->delete($entity);
        $this->afterRemove($entity, $options);
    }

    
    public function find(?array $params = []): Collection
    {
        return $this->createSelectBuilder()->find($params);
    }

    
    public function findOne(?array $params = []): ?Entity
    {
        $collection = $this->limit(0, 1)->find($params);

        foreach ($collection as $entity) {
            return $entity;
        }

        return null;
    }

    
    public function findBySql(string $sql): SthCollection
    {
        $mapper = $this->getMapper();

        if (!$mapper instanceof BaseMapper) {
            throw new RuntimeException("Not supported 'findBySql'.");
        }

        
        return $mapper->selectBySql($this->entityType, $sql);
    }

    
    public function count(array $params = []): int
    {
        return $this->createSelectBuilder()->count($params);
    }

    
    public function max(string $attribute)
    {
        return $this->createSelectBuilder()->max($attribute);
    }

    
    public function min(string $attribute)
    {
        return $this->createSelectBuilder()->min($attribute);
    }

    
    public function sum(string $attribute)
    {
        return $this->createSelectBuilder()->sum($attribute);
    }

    
    public function clone(Select $query): RDBSelectBuilder
    {
        if ($this->entityType !== $query->getFrom()) {
            throw new RuntimeException("Can't clone a query of a different entity type.");
        }

        
        $builder = new RDBSelectBuilder($this->entityManager, $this->entityType, $query);

        return $builder;
    }

    
    public function join($target, ?string $alias = null, $conditions = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->join($target, $alias, $conditions);
    }

    
    public function leftJoin($target, ?string $alias = null, $conditions = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->leftJoin($target, $alias, $conditions);
    }

    
    public function distinct(): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->distinct();
    }

    
    public function forUpdate(): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->forUpdate();
    }

    
    public function sth(): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->sth();
    }

    
    public function where($clause = [], $value = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->where($clause, $value);
    }

    
    public function having($clause = [], $value = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->having($clause, $value);
    }

    
    public function order($orderBy = 'id', $direction = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->order($orderBy, $direction);
    }

    
    public function limit(?int $offset = null, ?int $limit = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->limit($offset, $limit);
    }

    
    public function select($select = [], ?string $alias = null): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->select($select, $alias);
    }

    
    public function group($groupBy): RDBSelectBuilder
    {
        return $this->createSelectBuilder()->group($groupBy);
    }

    
    protected function createSelectBuilder(): RDBSelectBuilder
    {
        
        $builder = new RDBSelectBuilder($this->entityManager, $this->entityType);

        return $builder;
    }

    
    protected function beforeSave(Entity $entity, array $options = [])
    {
        $this->hookMediator->beforeSave($entity, $options);
    }

    
    protected function afterSave(Entity $entity, array $options = [])
    {
        $this->hookMediator->afterSave($entity, $options);
    }

    
    protected function beforeRemove(Entity $entity, array $options = [])
    {
        $this->hookMediator->beforeRemove($entity, $options);
    }

    
    protected function afterRemove(Entity $entity, array $options = [])
    {
        $this->hookMediator->afterRemove($entity, $options);
    }

    protected function getMapper(): RDBMapper
    {
        $mapper = $this->entityManager->getMapper();

        if (!$mapper instanceof RDBMapper) {
            throw new RuntimeException("Mapper is not RDB.");
        }

        return $mapper;
    }

    
    protected function beforeRelate(Entity $entity, $relationName, $foreign, $data = null, array $options = [])
    {}

    
    protected function afterRelate(Entity $entity, $relationName, $foreign, $data = null, array $options = [])
    {}

    
    protected function beforeUnrelate(Entity $entity, $relationName, $foreign, array $options = [])
    {}

    
    protected function afterUnrelate(Entity $entity, $relationName, $foreign, array $options = [])
    {}

    
    protected function beforeMassRelate(Entity $entity, $relationName, array $params = [], array $options = [])
    {}

    
    protected function afterMassRelate(Entity $entity, $relationName, array $params = [], array $options = [])
    {}
}
