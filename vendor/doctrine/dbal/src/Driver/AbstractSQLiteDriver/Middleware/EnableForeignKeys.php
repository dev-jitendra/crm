<?php

namespace Doctrine\DBAL\Driver\AbstractSQLiteDriver\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

class EnableForeignKeys implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        return new class ($driver) extends AbstractDriverMiddleware {
            
            public function connect(array $params): Connection
            {
                $connection = parent::connect($params);

                $connection->exec('PRAGMA foreign_keys=ON');

                return $connection;
            }
        };
    }
}
