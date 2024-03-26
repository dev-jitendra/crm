<?php

namespace Doctrine\DBAL\Driver;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\AbstractOracleDriver\EasyConnectString;
use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\API\OCI;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Schema\OracleSchemaManager;
use Doctrine\Deprecations\Deprecation;

use function assert;


abstract class AbstractOracleDriver implements Driver
{
    
    public function getDatabasePlatform()
    {
        return new OraclePlatform();
    }

    
    public function getSchemaManager(Connection $conn, AbstractPlatform $platform)
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'AbstractOracleDriver::getSchemaManager() is deprecated.'
                . ' Use OraclePlatform::createSchemaManager() instead.',
        );

        assert($platform instanceof OraclePlatform);

        return new OracleSchemaManager($conn, $platform);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        return new OCI\ExceptionConverter();
    }

    
    protected function getEasyConnectString(array $params)
    {
        return (string) EasyConnectString::fromConnectionParameters($params);
    }
}
