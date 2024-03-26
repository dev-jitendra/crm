<?php

namespace Doctrine\DBAL\Tools\Console;

use Doctrine\DBAL\Connection;

interface ConnectionProvider
{
    public function getDefaultConnection(): Connection;

    
    public function getConnection(string $name): Connection;
}
