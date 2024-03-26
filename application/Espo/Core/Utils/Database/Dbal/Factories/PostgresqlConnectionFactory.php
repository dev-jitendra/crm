<?php


namespace Espo\Core\Utils\Database\Dbal\Factories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDO\PgSQL\Driver as PostgreSQLDriver;
use Doctrine\DBAL\Exception as DBALException;
use Espo\Core\Utils\Database\Dbal\ConnectionFactory;
use Espo\Core\Utils\Database\Dbal\Platforms\PostgresqlPlatform;
use Espo\Core\Utils\Database\Helper;
use Espo\ORM\DatabaseParams;
use Espo\ORM\PDO\Options as PdoOptions;

use PDO;
use RuntimeException;

class PostgresqlConnectionFactory implements ConnectionFactory
{
    private const DEFAULT_CHARSET = 'utf8';

    public function __construct(
        private PDO $pdo,
        private Helper $helper
    ) {}

    
    public function create(DatabaseParams $databaseParams): Connection
    {
        $driver = new PostgreSQLDriver();

        if (!$databaseParams->getHost()) {
            throw new RuntimeException("No database host in config.");
        }

        $platform = new PostgresqlPlatform();

        if ($databaseParams->getName()) {
            $platform->setTextSearchConfig($this->helper->getParam('default_text_search_config'));
        }

        $params = [
            'platform' => $platform,
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
