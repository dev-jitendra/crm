<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Log;

trait LogSetter
{
    
    protected $log;

    public function setLog(Log $log): void
    {
        $this->log = $log;
    }
}
