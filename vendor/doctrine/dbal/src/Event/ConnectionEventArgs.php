<?php

namespace Doctrine\DBAL\Event;

use Doctrine\Common\EventArgs;
use Doctrine\DBAL\Connection;


class ConnectionEventArgs extends EventArgs
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    
    public function getConnection()
    {
        return $this->connection;
    }
}
