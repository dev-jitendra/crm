<?php

namespace Doctrine\DBAL;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Driver\IBMDB2;
use Doctrine\DBAL\Driver\Mysqli;
use Doctrine\DBAL\Driver\OCI8;
use Doctrine\DBAL\Driver\PDO;
use Doctrine\DBAL\Driver\SQLite3;
use Doctrine\DBAL\Driver\SQLSrv;
use Doctrine\Deprecations\Deprecation;

use function array_keys;
use function array_merge;
use function assert;
use function class_implements;
use function in_array;
use function is_a;
use function is_string;
use function parse_str;
use function parse_url;
use function preg_replace;
use function rawurldecode;
use function str_replace;
use function strpos;
use function substr;


final class DriverManager
{
    
    private const DRIVER_MAP = [
        'pdo_mysql'          => PDO\MySQL\Driver::class,
        'pdo_sqlite'         => PDO\SQLite\Driver::class,
        'pdo_pgsql'          => PDO\PgSQL\Driver::class,
        'pdo_oci'            => PDO\OCI\Driver::class,
        'oci8'               => OCI8\Driver::class,
        'ibm_db2'            => IBMDB2\Driver::class,
        'pdo_sqlsrv'         => PDO\SQLSrv\Driver::class,
        'mysqli'             => Mysqli\Driver::class,
        'sqlsrv'             => SQLSrv\Driver::class,
        'sqlite3'            => SQLite3\Driver::class,
    ];

    
    private static array $driverSchemeAliases = [
        'db2'        => 'ibm_db2',
        'mssql'      => 'pdo_sqlsrv',
        'mysql'      => 'pdo_mysql',
        'mysql2'     => 'pdo_mysql', 
        'postgres'   => 'pdo_pgsql',
        'postgresql' => 'pdo_pgsql',
        'pgsql'      => 'pdo_pgsql',
        'sqlite'     => 'pdo_sqlite',
        'sqlite3'    => 'pdo_sqlite',
    ];

    
    private function __construct()
    {
    }

    
    public static function getConnection(
        array $params,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ): Connection {
        
        $config       ??= new Configuration();
        $eventManager ??= new EventManager();
        $params         = self::parseDatabaseUrl($params);

        
        if (isset($params['primary'])) {
            $params['primary'] = self::parseDatabaseUrl($params['primary']);
        }

        if (isset($params['replica'])) {
            foreach ($params['replica'] as $key => $replicaParams) {
                $params['replica'][$key] = self::parseDatabaseUrl($replicaParams);
            }
        }

        $driver = self::createDriver($params);

        foreach ($config->getMiddlewares() as $middleware) {
            $driver = $middleware->wrap($driver);
        }

        $wrapperClass = $params['wrapperClass'] ?? Connection::class;
        if (! is_a($wrapperClass, Connection::class, true)) {
            throw Exception::invalidWrapperClass($wrapperClass);
        }

        return new $wrapperClass($params, $driver, $config, $eventManager);
    }

    
    public static function getAvailableDrivers(): array
    {
        return array_keys(self::DRIVER_MAP);
    }

    
    private static function createDriver(array $params): Driver
    {
        if (isset($params['driverClass'])) {
            $interfaces = class_implements($params['driverClass']);

            if ($interfaces === false || ! in_array(Driver::class, $interfaces, true)) {
                throw Exception::invalidDriverClass($params['driverClass']);
            }

            return new $params['driverClass']();
        }

        if (isset($params['driver'])) {
            if (! isset(self::DRIVER_MAP[$params['driver']])) {
                throw Exception::unknownDriver($params['driver'], array_keys(self::DRIVER_MAP));
            }

            $class = self::DRIVER_MAP[$params['driver']];

            return new $class();
        }

        throw Exception::driverRequired();
    }

    
    private static function normalizeDatabaseUrlPath(string $urlPath): string
    {
        
        return substr($urlPath, 1);
    }

    
    private static function parseDatabaseUrl(array $params): array
    {
        if (! isset($params['url'])) {
            return $params;
        }

        
        $url = preg_replace('#^((?:pdo_)?sqlite3?):
        assert($url !== null);

        $url = parse_url($url);

        if ($url === false) {
            throw new Exception('Malformed parameter "url".');
        }

        foreach ($url as $param => $value) {
            if (! is_string($value)) {
                continue;
            }

            $url[$param] = rawurldecode($value);
        }

        $params = self::parseDatabaseUrlScheme($url['scheme'] ?? null, $params);

        if (isset($url['host'])) {
            $params['host'] = $url['host'];
        }

        if (isset($url['port'])) {
            $params['port'] = $url['port'];
        }

        if (isset($url['user'])) {
            $params['user'] = $url['user'];
        }

        if (isset($url['pass'])) {
            $params['password'] = $url['pass'];
        }

        $params = self::parseDatabaseUrlPath($url, $params);
        $params = self::parseDatabaseUrlQuery($url, $params);

        return $params;
    }

    
    private static function parseDatabaseUrlPath(array $url, array $params): array
    {
        if (! isset($url['path'])) {
            return $params;
        }

        $url['path'] = self::normalizeDatabaseUrlPath($url['path']);

        
        
        if (! isset($params['driver'])) {
            return self::parseRegularDatabaseUrlPath($url, $params);
        }

        if (strpos($params['driver'], 'sqlite') !== false) {
            return self::parseSqliteDatabaseUrlPath($url, $params);
        }

        return self::parseRegularDatabaseUrlPath($url, $params);
    }

    
    private static function parseDatabaseUrlQuery(array $url, array $params): array
    {
        if (! isset($url['query'])) {
            return $params;
        }

        $query = [];

        parse_str($url['query'], $query); 

        return array_merge($params, $query); 
    }

    
    private static function parseRegularDatabaseUrlPath(array $url, array $params): array
    {
        $params['dbname'] = $url['path'];

        return $params;
    }

    
    private static function parseSqliteDatabaseUrlPath(array $url, array $params): array
    {
        if ($url['path'] === ':memory:') {
            $params['memory'] = true;

            return $params;
        }

        $params['path'] = $url['path']; 

        return $params;
    }

    
    private static function parseDatabaseUrlScheme(?string $scheme, array $params): array
    {
        if ($scheme !== null) {
            
            
            unset($params['driverClass']);

            
            $driver = str_replace('-', '_', $scheme);

            
            
            if (isset(self::$driverSchemeAliases[$driver])) {
                $actualDriver = self::$driverSchemeAliases[$driver];

                Deprecation::trigger(
                    'doctrine/dbal',
                    'https:
                    'Relying on driver name aliases is deprecated. Use %s instead of %s.',
                    str_replace('_', '-', $actualDriver),
                    $driver,
                );

                $driver = $actualDriver;
            }

            
            
            $params['driver'] = $driver;

            return $params;
        }

        
        
        if (! isset($params['driverClass']) && ! isset($params['driver'])) {
            throw Exception::driverRequired($params['url']);
        }

        return $params;
    }
}
