<?php

namespace Laravel\SerializableClosure\Exceptions;

use Exception;

class PhpVersionNotSupportedException extends Exception
{
    
    public function __construct($message = 'PHP 7.3 is not supported.')
    {
        parent::__construct($message);
    }
}
