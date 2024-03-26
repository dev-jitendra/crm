<?php


namespace Espo\Core\ORM;

use Espo\Core\Utils\Config;
use Espo\ORM\DatabaseParams;

use RuntimeException;

class DatabaseParamsFactory
{
    private const DEFAULT_PLATFORM = 'Mysql';

    public function __construct(private Config $config) {}

    public function create(): DatabaseParams
    {
        $config = $this->config;

        if (!$config->get('database')) {
            throw new RuntimeException('No database params in config.');
        }

        $databaseParams = DatabaseParams::create()
            ->withHost($config->get('database.host'))
            ->withPort($config->get('database.port') ? (int) $config->get('database.port') : null)
            ->withName($config->get('database.dbname'))
            ->withUsername($config->get('database.user'))
            ->withPassword($config->get('database.password'))
            ->withCharset($config->get('database.charset'))
            ->withPlatform($config->get('database.platform'))
            ->withSslCa($config->get('database.sslCA'))
            ->withSslCert($config->get('database.sslCert'))
            ->withSslKey($config->get('database.sslKey'))
            ->withSslCaPath($config->get('database.sslCAPath'))
            ->withSslCipher($config->get('database.sslCipher'))
            ->withSslVerifyDisabled($config->get('database.sslVerifyDisabled') ?? false);

        if (!$databaseParams->getPlatform()) {
            $databaseParams = $databaseParams->withPlatform(self::DEFAULT_PLATFORM);
        }

        return $databaseParams;
    }

    
    public function createWithMergedAssoc(array $params): DatabaseParams
    {
        $configParams = $this->create();

        return DatabaseParams::create()
            ->withHost($params['host'] ?? $configParams->getHost())
            ->withPort(isset($params['port']) ? (int) $params['port'] : $configParams->getPort())
            ->withName($params['dbname'] ?? $configParams->getName())
            ->withUsername($params['user'] ?? $configParams->getUsername())
            ->withPassword($params['password'] ?? $configParams->getPassword())
            ->withCharset($params['charset'] ?? $configParams->getCharset())
            ->withPlatform($params['platform'] ?? $configParams->getPlatform())
            ->withSslCa($params['sslCA'] ?? $configParams->getSslCa())
            ->withSslCert($params['sslCert'] ?? $configParams->getSslCert())
            ->withSslKey($params['sslKey'] ?? $configParams->getSslKey())
            ->withSslCaPath($params['sslCAPath'] ?? $configParams->getSslCaPath())
            ->withSslCipher($params['sslCipher'] ?? $configParams->getSslCipher())
            ->withSslVerifyDisabled($params['sslVerifyDisabled'] ?? $configParams->isSslVerifyDisabled());
    }
}
