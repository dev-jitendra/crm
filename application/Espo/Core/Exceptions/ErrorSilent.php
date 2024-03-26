<?php


namespace Espo\Core\Exceptions;

use Espo\Core\Utils\Log;

class ErrorSilent extends Error implements HasLogLevel
{
    public function getLogLevel(): string
    {
        return Log::LEVEL_NOTICE;
    }
}
