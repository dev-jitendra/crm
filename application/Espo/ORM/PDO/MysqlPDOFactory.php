<?php


namespace Espo\ORM\PDO;

use Espo\ORM\DatabaseParams;
use PDO;
use RuntimeException;

class MysqlPDOFactory implements PDOFactory
{
    private const DEFAULT_CHARSET = 'utf8mb4';

    public function create(DatabaseParams $databaseParams): PDO
    {
        $platform = strtolower($databaseParams->getPlatform() ?? '');

        $host = $databaseParams->getHost();
        $port = $databaseParams->getPort();
        $dbname = $databaseParams->getName();
        $charset = $databaseParams->getCharset() ?? self::DEFAULT_CHARSET;
        $username = $databaseParams->getUsername();
        $password = $databaseParams->getPassword();

        if (!$platform) {
            throw new RuntimeException("No 'platform' parameter.");
        }

        if (!$host) {
            throw new RuntimeException("No 'host' parameter.");
        }

        $dsn = $platform . ':' . 'host=' . $host;

        if ($port) {
            $dsn .= ';' . 'port=' . (string) $port;
        }

        if ($dbname) {
            $dsn .= ';' . 'dbname=' . $dbname;
        }

        $dsn .= ';' . 'charset=' . $charset;

        $options = Options::getOptionsFromDatabaseParams($databaseParams);

        $pdo = new PDO($dsn, $username, $password, $options);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}
