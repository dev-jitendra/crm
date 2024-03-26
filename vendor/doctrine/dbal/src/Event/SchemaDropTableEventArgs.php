<?php

namespace Doctrine\DBAL\Event;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Table;
use InvalidArgumentException;


class SchemaDropTableEventArgs extends SchemaEventArgs
{
    
    private $table;

    private AbstractPlatform $platform;

    
    private $sql;

    
    public function __construct($table, AbstractPlatform $platform)
    {
        $this->table    = $table;
        $this->platform = $platform;
    }

    
    public function getTable()
    {
        return $this->table;
    }

    
    public function getPlatform()
    {
        return $this->platform;
    }

    
    public function setSql($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    
    public function getSql()
    {
        return $this->sql;
    }
}
