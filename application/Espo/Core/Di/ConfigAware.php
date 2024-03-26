<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Config;

interface ConfigAware
{
    public function setConfig(Config $config): void;
}
