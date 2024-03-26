<?php

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;


class SchemaColumnDefinitionEventArgs extends SchemaEventArgs
{
    private ?Column $column = null;

    
    private $tableColumn;

    
    private $table;

    
    private $database;

    private Connection $connection;

    
    public function __construct(array $tableColumn, $table, $database, Connection $connection)
    {
        $this->tableColumn = $tableColumn;
        $this->table       = $table;
        $this->database    = $database;
        $this->connection  = $connection;
    }

    
    public function setColumn(?Column $column = null)
    {
        $this->column = $column;

        return $this;
    }

    
    public function getColumn()
    {
        return $this->column;
    }

    
    public function getTableColumn()
    {
        return $this->tableColumn;
    }

    
    public function getTable()
    {
        return $this->table;
    }

    
    public function getDatabase()
    {
        return $this->database;
    }

    
    public function getConnection()
    {
        return $this->connection;
    }
}
