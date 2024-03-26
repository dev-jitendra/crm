<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Log;

interface LogAware
{
    public function setLog(Log $log): void;
}
