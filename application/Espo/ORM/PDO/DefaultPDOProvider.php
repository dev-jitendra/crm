<?php


namespace Espo\ORM\PDO;

use Espo\ORM\DatabaseParams;
use PDO;

class DefaultPDOProvider implements PDOProvider
{
    private ?PDO $pdo = null;

    public function __construct(
        private DatabaseParams $databaseParams,
        private PDOFactory $pdoFactory
    ) {}

    public function get(): PDO
    {
        if (!$this->pdo) {
            $this->intPDO();
        }

        assert($this->pdo !== null);

        return $this->pdo;
    }

    private function intPDO(): void
    {
        $this->pdo = $this->pdoFactory->create($this->databaseParams);
    }
}
