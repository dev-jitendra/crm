<?php


namespace Espo\Core\Exceptions;

use Exception;

class Unauthorized extends Exception
{
    
    protected $code = 401;
}
