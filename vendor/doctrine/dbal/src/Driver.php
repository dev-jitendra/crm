<?php

namespace Doctrine\DBAL;

use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection as DriverConnection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\AbstractSchemaManager;


interface Driver
{
    
    public function connect(array $params);

    
    public function getDatabasePlatform();

    
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform);

    
    public function getExceptionConverter(): ExceptionConverter;
}
