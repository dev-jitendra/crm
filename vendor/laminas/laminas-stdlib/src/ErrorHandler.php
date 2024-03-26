<?php 


declare(strict_types=1);

namespace Laminas\Stdlib;

use ErrorException;

use function array_pop;
use function count;
use function restore_error_handler;
use function set_error_handler;

use const E_WARNING;


abstract class ErrorHandler
{
    
    protected static $stack = [];

    
    public static function started()
    {
        return (bool) static::getNestedLevel();
    }

    
    public static function getNestedLevel()
    {
        return count(static::$stack);
    }

    
    public static function start($errorLevel = E_WARNING)
    {
        if (! static::$stack) {
            set_error_handler([static::class, 'addError'], $errorLevel);
        }

        static::$stack[] = null;
    }

    
    public static function stop($throw = false)
    {
        $errorException = null;

        if (static::$stack) {
            $errorException = array_pop(static::$stack);

            if (! static::$stack) {
                restore_error_handler();
            }

            if ($errorException && $throw) {
                throw $errorException;
            }
        }

        return $errorException;
    }

    
    public static function clean()
    {
        if (static::$stack) {
            restore_error_handler();
        }

        static::$stack = [];
    }

    
    public static function addError($errno, $errstr = '', $errfile = '', $errline = 0)
    {
        $stack = &static::$stack[count(static::$stack) - 1];
        $stack = new ErrorException($errstr, 0, $errno, $errfile, $errline, $stack);
    }
}
