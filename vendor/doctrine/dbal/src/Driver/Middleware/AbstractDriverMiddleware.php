<?php

namespace Doctrine\DBAL\Driver\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\VersionAwarePlatformDriver;
use Doctrine\Deprecations\Deprecation;

abstract class AbstractDriverMiddleware implements VersionAwarePlatformDriver
{
    private Driver $wrappedDriver;

    public function __construct(Driver $wrappedDriver)
    {
        $this->wrappedDriver = $wrappedDriver;
    }

    
    public function connect(array $params)
    {
        return $this->wrappedDriver->connect($params);
    }

    
    public function getDatabasePlatform()
    {
        return $this->wrappedDriver->getDatabasePlatform();
    }

    
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'AbstractDriverMiddleware::getSchemaManager() is deprecated.'
                . ' Use AbstractPlatform::createSchemaManager() instead.',
        );

        return $this->wrappedDriver->getSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return $this->wrappedDriver->getExceptionConverter();
    }

    
    public function createDatabasePlatformForVersion($version)
    {
        if ($this->wrappedDriver instanceof VersionAwarePlatformDriver) {
            return $this->wrappedDriver->createDatabasePlatformForVersion($version);
        }

        return $this->wrappedDriver->getDatabasePlatform();
    }
}
