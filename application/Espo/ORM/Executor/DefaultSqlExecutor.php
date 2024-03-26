<?php


namespace Espo\ORM\Executor;

use Espo\ORM\PDO\PDOProvider;
use Psr\Log\LoggerInterface;

use PDO;
use PDOStatement;
use PDOException;
use Exception;
use RuntimeException;

class DefaultSqlExecutor implements SqlExecutor
{
    private const MAX_ATTEMPT_COUNT = 4;

    private PDO $pdo;

    public function __construct(
        PDOProvider $pdoProvider,
        private ?LoggerInterface $logger = null,
        private bool $logAll = false
    ) {
        $this->pdo = $pdoProvider->get();
    }

    
    public function execute(string $sql, bool $rerunIfDeadlock = false): PDOStatement
    {
        if ($this->logAll) {
            $this->logger?->info("SQL: " . $sql);
        }

        if (!$rerunIfDeadlock) {
            return $this->executeSqlWithDeadlockHandling($sql, 1);
        }

        return $this->executeSqlWithDeadlockHandling($sql);
    }

    private function executeSqlWithDeadlockHandling(string $sql, ?int $counter = null): PDOStatement
    {
        $counter = $counter ?? self::MAX_ATTEMPT_COUNT;

        try {
            $sth = $this->pdo->query($sql);
        }
        catch (Exception $e) {
            $counter--;

            if ($counter === 0 || !$this->isExceptionIsDeadlock($e)) {
                
                throw $e;
            }

            return $this->executeSqlWithDeadlockHandling($sql, $counter);
        }

        if (!$sth) {
            throw new RuntimeException("Query execution failure.");
        }

        return $sth;
    }

    private function isExceptionIsDeadlock(Exception $e): bool
    {
        if (!$e instanceof PDOException) {
            return false;
        }

        return isset($e->errorInfo) && $e->errorInfo[0] == 40001 && $e->errorInfo[1] == 1213;
    }
}
