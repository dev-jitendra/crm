<?php

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Index;


class SchemaIndexDefinitionEventArgs extends SchemaEventArgs
{
    private ?Index $index = null;

    
    private array $tableIndex;

    
    private $table;

    private Connection $connection;

    
    public function __construct(array $tableIndex, $table, Connection $connection)
    {
        $this->tableIndex = $tableIndex;
        $this->table      = $table;
        $this->connection = $connection;
    }

    
    public function setIndex(?Index $index = null)
    {
        $this->index = $index;

        return $this;
    }

    
    public function getIndex()
    {
        return $this->index;
    }

    
    public function getTableIndex()
    {
        return $this->tableIndex;
    }

    
    public function getTable()
    {
        return $this->table;
    }

    
    public function getConnection()
    {
        return $this->connection;
    }
}
