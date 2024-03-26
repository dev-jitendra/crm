<?php


namespace Espo\ORM;

use Espo\ORM\Query\Select as SelectQuery;

use IteratorAggregate;
use Countable;
use stdClass;
use Traversable;
use PDO;
use PDOStatement;
use RuntimeException;
use LogicException;


class SthCollection implements Collection, IteratorAggregate, Countable
{
    private string $entityType;
    private ?SelectQuery $query = null;
    private ?PDOStatement $sth = null;
    private ?string $sql = null;

    private function __construct(private EntityManager $entityManager)
    {}

    private function executeQuery(): void
    {
        if ($this->query) {
            $this->sth = $this->entityManager->getQueryExecutor()->execute($this->query);

            return;
        }

        if (!$this->sql) {
            throw new LogicException("No query & sql.");
        }

        $this->sth = $this->entityManager->getSqlExecutor()->execute($this->sql);
    }

    public function getIterator(): Traversable
    {
        return (function () {
            if (isset($this->sth)) {
                $this->sth->execute();
            }

            while ($row = $this->fetchRow()) {
                $entity = $this->entityManager->getEntityFactory()->create($this->entityType);

                $entity->set($row);
                $entity->setAsFetched();

                $this->prepareEntity($entity);

                yield $entity;
            }
        })();
    }

    private function executeQueryIfNotExecuted(): void
    {
        if (!$this->sth) {
            $this->executeQuery();
        }
    }

    
    private function fetchRow()
    {
        $this->executeQueryIfNotExecuted();

        assert($this->sth !== null);

        return $this->sth->fetch(PDO::FETCH_ASSOC);
    }

    
    public function count(): int
    {
        $this->executeQueryIfNotExecuted();

        assert($this->sth !== null);

        $rowCount = $this->sth->rowCount();

        
        if ($rowCount) {
            return $rowCount;
        }

        return iterator_count($this);
    }

    protected function prepareEntity(Entity $entity): void
    {}

    
    public function toArray(bool $itemsAsObjects = false): array
    {
        $arr = [];

        foreach ($this as $entity) {
            $item = $entity->getValueMap();

            if (!$itemsAsObjects) {
                $item = get_object_vars($item);
            }

            $arr[] = $item;
        }

        return $arr;
    }

    
    public function getValueMapList(): array
    {
        
        return $this->toArray(true);
    }

    
    public function isFetched(): bool
    {
        return true;
    }

    
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    
    public static function fromQuery(SelectQuery $query, EntityManager $entityManager): self
    {
        
        $obj = new self($entityManager);

        $entityType = $query->getFrom();

        if ($entityType === null) {
            throw new RuntimeException("Query w/o entity type.");
        }

        $obj->entityType = $entityType;
        $obj->query = $query;

        return $obj;
    }

    
    public static function fromSql(string $entityType, string $sql, EntityManager $entityManager): self
    {
        
        $obj = new self($entityManager);

        $obj->entityType = $entityType;
        $obj->sql = $sql;

        return $obj;
    }
}
