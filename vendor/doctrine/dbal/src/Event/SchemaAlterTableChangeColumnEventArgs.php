<?php

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\TableDiff;

use function array_merge;
use function func_get_args;
use function is_array;


class SchemaAlterTableChangeColumnEventArgs extends SchemaEventArgs
{
    private ColumnDiff $columnDiff;
    private TableDiff $tableDiff;
    private AbstractPlatform $platform;

    
    private array $sql = [];

    public function __construct(ColumnDiff $columnDiff, TableDiff $tableDiff, AbstractPlatform $platform)
    {
        $this->columnDiff = $columnDiff;
        $this->tableDiff  = $tableDiff;
        $this->platform   = $platform;
    }

    
    public function getColumnDiff()
    {
        return $this->columnDiff;
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
