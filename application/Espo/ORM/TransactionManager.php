<?php


namespace Espo\ORM;

use Espo\ORM\QueryComposer\QueryComposer;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;
use Closure;

class TransactionManager
{
    private int $level = 0;

    public function __construct(private PDO $pdo, private QueryComposer $queryComposer)
    {}

    
    public function isStarted(): bool
    {
        return $this->level > 0;
    }

    
    public function getLevel(): int
    {
        return $this->level;
    }

    
    public function run(Closure $function)
    {
        $this->start();

        try {
            $result = $function();

            $this->commit();
        }
        catch (Throwable $e) {
            $this->rollback();

            
            throw $e;
        }

        return $result;
    }

    
    public function start(): void
    {
        if ($this->level > 0) {
            $this->createSavepoint();

            $this->level++;

            return;
        }

        $this->pdo->beginTransaction();

        $this->level++;
    }

    
    public function commit(): void
    {
        if ($this->level === 0) {
            throw new RuntimeException("Can't commit not started transaction.");
        }

        $this->level--;

        if ($this->level > 0) {
            $this->releaseSavepoint();

            return;
        }

        $this->pdo->commit();
    }

    
    public function rollback(): void
    {
        if ($this->level === 0) {
            throw new RuntimeException("Can't rollback not started transaction.");
        }

        $this->level--;

        if ($this->level > 0) {
            $this->rollbackToSavepoint();

            return;
        }

        $this->pdo->rollBack();
    }

    private function getCurrentSavepoint(): string
    {
        return 'POINT_' . (string) $this->level;
    }

    private function createSavepoint(): void
    {
        $sql = $this->queryComposer->composeCreateSavepoint($this->getCurrentSavepoint());

        $this->pdo->exec($sql);
    }

    private function releaseSavepoint(): void
    {
        $sql = $this->queryComposer->composeReleaseSavepoint($this->getCurrentSavepoint());

        $this->pdo->exec($sql);
    }

    private function rollbackToSavepoint(): void
    {
        $sql = $this->queryComposer->composeRollbackToSavepoint($this->getCurrentSavepoint());

        $this->pdo->exec($sql);
    }
}
