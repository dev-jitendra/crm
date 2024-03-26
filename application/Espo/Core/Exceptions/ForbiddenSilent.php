<?php


namespace Espo\Core\Exceptions;

use Espo\Core\Utils\Log;

class ForbiddenSilent extends Forbidden implements HasLogLevel
{
    public function getLogLevel(): string
    {
        return Log::LEVEL_NOTICE;
    }
}
