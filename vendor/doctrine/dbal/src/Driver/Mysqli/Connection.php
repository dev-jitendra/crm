<?php

namespace Doctrine\DBAL\Driver\Mysqli;

use Doctrine\DBAL\Driver\Mysqli\Exception\ConnectionError;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\Driver\Statement as DriverStatement;
use Doctrine\DBAL\ParameterType;
use Doctrine\Deprecations\Deprecation;
use mysqli;
use mysqli_sql_exception;

final class Connection implements ServerInfoAwareConnection
{
    
    public const OPTION_FLAGS = 'flags';

    private mysqli $connection;

    
    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    
    public function getWrappedResourceHandle(): mysqli
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated, call getNativeConnection() instead.',
            __METHOD__,
        );

        return $this->getNativeConnection();
    }

    public function getServerVersion(): string
    {
        return $this->connection->get_server_info();
    }

    public function prepare(string $sql): DriverStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
        } catch (mysqli_sql_exception $e) {
            throw ConnectionError::upcast($e);
        }

        if ($stmt === false) {
            throw ConnectionError::new($this->connection);
        }

        return new Statement($stmt);
    }

    public function query(string $sql): ResultInterface
    {
        return $this->prepare($sql)->execute();
    }

    
    public function quote($value, $type = ParameterType::STRING)
    {
        return "'" . $this->connection->escape_string($value) . "'";
    }

    public function exec(string $sql): int
    {
        try {
            $result = $this->connection->query($sql);
        } catch (mysqli_sql_exception $e) {
            throw ConnectionError::upcast($e);
        }

        if ($result === false) {
            throw ConnectionError::new($this->connection);
        }

        return $this->connection->affected_rows;
    }

    
    public function lastInsertId($name = null)
    {
        if ($name !== null) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/dbal',
                'https:
                'The usage of Connection::lastInsertId() with a sequence name is deprecated.',
            );
        }

        return $this->connection->insert_id;
    }

    public function beginTransaction(): bool
    {
        $this->connection->begin_transaction();

        return true;
    }

    public function commit(): bool
    {
        try {
            return $this->connection->commit();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public function rollBack(): bool
    {
        try {
            return $this->connection->rollback();
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public function getNativeConnection(): mysqli
    {
        return $this->connection;
    }
}
