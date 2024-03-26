<?php

namespace Laravel\SerializableClosure\Exceptions;

use Exception;

class MissingSecretKeyException extends Exception
{
    
    public function __construct($message = 'No serializable closure secret key has been specified.')
    {
        parent::__construct($message);
    }
}
