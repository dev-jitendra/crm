<?php


namespace Espo\Core\Utils\Database\DetailsProviders;

use Espo\Core\Utils\Database\DetailsProvider;
use PDO;

class PostgresqlDetailsProvider implements DetailsProvider
{
    private const TYPE_POSTGRESQL = 'PostgreSQL';

    public function __construct(private PDO $pdo)
    {}

    public function getType(): string
    {
       return self::TYPE_POSTGRESQL;
    }

    public function getVersion(): string
    {
        $fullVersion = $this->getFullDatabaseVersion() ?? '';

        if (preg_match('/[0-9]+\.[0-9]+/', $fullVersion, $match)) {
            return $match[0];
        }

        return '0.0';
    }

    public function getServerVersion(): string
    {
        return (string) $this->getFullDatabaseVersion();
    }

    public function getParam(string $name): ?string
    {
        $name = preg_replace('/[^A-Za-z0-9_]+/', '', $name);;

        $sql = "SHOW {$name}";

        $sth = $this->pdo->query($sql);

        if ($sth === false) {
            return null;
        }

        $row = $sth->fetch(PDO::FETCH_NUM);

        if ($row === false) {
            return null;
        }

        $value = $row[0] ?: null;

        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    private function getFullDatabaseVersion(): ?string
    {
        $sql = "select version()";

        $sth = $this->pdo->prepare($sql);
        $sth->execute();

        
        $result = $sth->fetchColumn();

        if ($result === false || $result === null) {
            return null;
        }

        return $result;
    }
}
