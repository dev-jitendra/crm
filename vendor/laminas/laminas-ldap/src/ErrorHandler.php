<?php

namespace Laminas\Ldap;

use function restore_error_handler;
use function set_error_handler;

use const E_WARNING;


class ErrorHandler implements ErrorHandlerInterface
{
    
    protected static $errorHandler;

    
    public static function start($level = E_WARNING)
    {
        self::getErrorHandler()->startErrorHandling($level);
    }

    
    public static function stop($throw = false)
    {
        return self::getErrorHandler()->stopErrorHandling($throw);
    }

    
    protected static function getErrorHandler()
    {
        if (! self::$errorHandler && ! self::$errorHandler instanceof ErrorHandlerInterface) {
            self::$errorHandler = new self();
        }

        return self::$errorHandler;
    }

    
    public function startErrorHandling($level = E_WARNING)
    {
        set_error_handler(static function ($errNo, $errString): void {
        });
    }

    
    public function stopErrorHandling($throw = false)
    {
        restore_error_handler();
    }

    
    public static function setErrorHandler(ErrorHandlerInterface $errorHandler)
    {
        self::$errorHandler = $errorHandler;
    }
}
