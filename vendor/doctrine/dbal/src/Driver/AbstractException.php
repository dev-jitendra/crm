<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver;

use Exception as BaseException;
use Throwable;


abstract class AbstractException extends BaseException implements Exception
{
    
    private ?string $sqlState = null;

    
    public function __construct($message, $sqlState = null, $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->sqlState = $sqlState;
    }

    
    public function getSQLState()
    {
        return $this->sqlState;
    }
}
