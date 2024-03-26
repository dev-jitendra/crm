<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Config;

trait ConfigSetter
{
    
    protected $config;

    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }
}
