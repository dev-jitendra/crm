<?php


namespace Espo\ORM\Locker;

use Espo\ORM\Query\LockTableBuilder;
use Espo\ORM\QueryComposer\QueryComposer;
use Espo\ORM\TransactionManager;

use PDO;
use RuntimeException;

class BaseLocker implements Locker
{
    private bool $isLocked = false;

    public function __construct(
        private PDO $pdo,
        private QueryComposer $queryComposer,
        private TransactionManager $transactionManager
    ) {}

    
    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    
    public function lockExclusive(string $entityType): void
    {
        $this->isLocked = true;

        $this->transactionManager->start();

        $query = (new LockTableBuilder())
            ->table($entityType)
            ->inExclusiveMode()
            ->build();

        $sql = $this->queryComposer->composeLockTable($query);

        $this->pdo->exec($sql);
    }

    
    public function lockShare(string $entityType): void
    {
        $this->isLocked = true;

        $this->transactionManager->start();

        $query = (new LockTableBuilder())
            ->table($entityType)
            ->inShareMode()
            ->build();

        $sql = $this->queryComposer->composeLockTable($query);

        $this->pdo->exec($sql);
    }

    
    public function commit(): void
    {
        if (!$this->isLocked) {
            throw new RuntimeException("Can't commit, it was not locked.");
        }

        $this->transactionManager->commit();

        $this->isLocked = false;
    }

    
    public function rollback(): void
    {
        if (!$this->isLocked) {
            throw new RuntimeException("Can't rollback, it was not locked.");
        }

        $this->transactionManager->rollback();

        $this->isLocked = false;
    }
}
