<?php

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;

use function array_merge;
use function func_get_args;
use function is_array;


class SchemaCreateTableEventArgs extends SchemaEventArgs
{
    private Table $table;

    
    private array $columns;

    
    private array $options;

    private AbstractPlatform $platform;

    
    private array $sql = [];

    
    public function __construct(Table $table, array $columns, array $options, AbstractPlatform $platform)
    {
        $this->table    = $table;
        $this->columns  = $columns;
        $this->options  = $options;
        $this->platform = $platform;
    }

    
    public function getTable()
    {
        return $this->table;
    }

    
    public function getColumns()
    {
        return $this->columns;
    }

    
    public function getOptions()
    {
        return $this->options;
    }

    
    public function getPlatform()
    {
        return $this->platform;
    }

    
    public function addSql($sql)
    {
        $this->sql = array_merge($this->sql, is_array($sql) ? $sql : func_get_args());

        return $this;
    }

    
    public function getSql()
    {
        return $this->sql;
    }
}
