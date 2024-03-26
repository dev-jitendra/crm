<?php


namespace Espo\Core\Utils\Database;

use Espo\Core\Utils\Config;

class DefaultConfigDataProvider implements ConfigDataProvider
{
    private const DEFAULT_PLATFORM = 'Mysql';

    public function __construct(private Config $config) {}

    public function getPlatform(): string
    {
        return $this->config->get('database.platform') ?? self::DEFAULT_PLATFORM;
    }
}
