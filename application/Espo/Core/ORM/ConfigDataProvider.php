<?php


namespace Espo\Core\ORM;

use Espo\Core\Utils\Config;

class ConfigDataProvider
{
    public function __construct(private Config $config)
    {}

    public function logSql(): bool
    {
        return (bool) $this->config->get('logger.sql');
    }
}
