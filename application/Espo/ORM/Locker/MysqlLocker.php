<?php


namespace Espo\ORM\Locker;

use Espo\ORM\QueryComposer\QueryComposer;
use Espo\ORM\QueryComposer\MysqlQueryComposer;
use Espo\ORM\Query\LockTableBuilder;
use Espo\ORM\TransactionManager;

use PDO;
use RuntimeException;


class MysqlLocker implements Locker
{
    private MysqlQueryComposer $queryComposer;
    
    private TransactionManager $transactionManager;

    private bool $isLocked = false;

    public function __construct(
        private PDO $pdo,
        QueryComposer $queryComposer,
        TransactionManager $transactionManager
    ) {
        $this->transactionManager = $transactionManager;

        if (!$queryComposer instanceof MysqlQueryComposer) {
            throw new RuntimeException();
        }

        $this->queryComposer = $queryComposer;
    }

    
    public function isLocked(): bool
    {
        return $this->isLocked;
    }
    
    public function lockExclusive(string $entityType): void
    {
        $this->isLocked = true;

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

        $this->isLocked = false;

        $sql = $this->queryComposer->composeUnlockTables();

        $this->pdo->exec($sql);
    }

    
    public function rollback(): void
    {
        if (!$this->isLocked) {
            throw new RuntimeException("Can't rollback, it was not locked.");
        }

        $this->isLocked = false;

        $sql = $this->queryComposer->composeUnlockTables();

        $this->pdo->exec($sql);
    }
}
