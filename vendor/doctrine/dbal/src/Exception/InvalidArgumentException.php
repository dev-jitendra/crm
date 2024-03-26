<?php

namespace Doctrine\DBAL\Exception;

use Doctrine\DBAL\Exception;


class InvalidArgumentException extends Exception
{
    
    public static function fromEmptyCriteria()
    {
        return new self('Empty criteria was used, expected non-empty criteria');
    }
}
