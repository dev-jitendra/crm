<?php

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\TableDiff;

use function array_merge;
use function func_get_args;
use function is_array;


class SchemaAlterTableRemoveColumnEventArgs extends SchemaEventArgs
{
    private Column $column;
    private TableDiff $tableDiff;
    private AbstractPlatform $platform;

    
    private array $sql = [];

    public function __construct(Column $column, TableDiff $tableDiff, AbstractPlatform $platform)
    {
        $this->column    = $column;
        $this->tableDiff = $tableDiff;
        $this->platform  = $platform;
    }

    
    public function getColumn()
    {
        return $this->column;
    }

    
    public function getTableDiff()
    {
        return $this->tableDiff;
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
