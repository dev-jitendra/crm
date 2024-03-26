<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\Log\LogLoader;
use Espo\Core\Utils\Log as LogService;

class Log implements Loader
{
    public function __construct(private LogLoader $logLoader)
    {}

    public function load(): LogService
    {
        $log = $this->logLoader->load();

        
        $GLOBALS['log'] = $log;

        return $log;
    }
}
