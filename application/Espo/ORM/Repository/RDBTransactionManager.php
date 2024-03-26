<?php


namespace Espo\ORM\Repository;

use Espo\ORM\TransactionManager;

use RuntimeException;


class RDBTransactionManager
{
    private int $level = 0;

    public function __construct(private TransactionManager $transactionManager)
    {}

    public function isStarted(): bool
    {
        return $this->level > 0;
    }

    public function start(): void
    {
        if ($this->isStarted()) {
            throw new RuntimeException("Can't start a transaction more than once.");
        }

        $this->transactionManager->start();

        $this->level = $this->transactionManager->getLevel();
    }

    public function commit(): void
    {
        if (!$this->isStarted()) {
            throw new RuntimeException("Can't commit not started transaction.");
        }

        while ($this->transactionManager->getLevel() >= $this->level) {
            $this->transactionManager->commit();
        }

        $this->level = 0;
    }

    public function rollback(): void
    {
        if (!$this->isStarted()) {
            throw new RuntimeException("Can't rollback not started transaction.");
        }

        while ($this->transactionManager->getLevel() >= $this->level) {
            $this->transactionManager->rollback();
        }

        $this->level = 0;
    }
}
