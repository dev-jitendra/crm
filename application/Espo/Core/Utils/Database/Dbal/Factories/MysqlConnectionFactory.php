<?php


namespace Espo\Core\Utils\Database\Dbal\Factories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\MySQL\Driver as PDOMySQLDriver;
use Doctrine\DBAL\Exception as DBALException;

use Espo\Core\Utils\Database\Dbal\ConnectionFactory;
use Espo\ORM\DatabaseParams;
use Espo\ORM\PDO\Options as PdoOptions;

use PDO;
use RuntimeException;

class MysqlConnectionFactory implements ConnectionFactory
{
    private const DEFAULT_CHARSET = 'utf8mb4';

    public function __construct(
        private PDO $pdo
    ) {}

    
    public function create(DatabaseParams $databaseParams): Connection
    {
        $driver = new PDOMySQLDriver();

        if (!$databaseParams->getHost()) {
            throw new RuntimeException("No database host in config.");
        }

        $params = [
            'pdo' => $this->pdo,
            'host' => $databaseParams->getHost(),
            'driverOptions' => PdoOptions::getOptionsFromDatabaseParams($databaseParams),
        ];

        if ($databaseParams->getName() !== null) {
            $params['dbname'] = $databaseParams->getName();
        }

        if ($databaseParams->getPort() !== null) {
            $params['port'] = $databaseParams->getPort();
        }

        if ($databaseParams->getUsername() !== null) {
            $params['user'] = $databaseParams->getUsername();
        }

        if ($databaseParams->getPassword() !== null) {
            $params['password'] = $databaseParams->getPassword();
        }

        $params['charset'] = $databaseParams->getCharset() ?? self::DEFAULT_CHARSET;

        return new Connection($params, $driver);
    }
}
