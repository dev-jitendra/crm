<?php


namespace Espo\Core\Exceptions;

use Espo\Core\Utils\Log;
use Exception;


class NotFound extends Exception implements HasLogLevel
{
    
    protected $code = 404;

    public function getLogLevel(): string
    {
        return Log::LEVEL_WARNING;
    }
}
