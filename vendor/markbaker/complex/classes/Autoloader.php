<?php

namespace Complex;


class Autoloader
{
    
    public static function Register()
    {
        if (function_exists('__autoload')) {
            
            spl_autoload_register('__autoload');
        }
        
        return spl_autoload_register(['Complex\\Autoloader', 'Load']);
    }


    
    public static function Load($pClassName)
    {
        if ((class_exists($pClassName, false)) || (strpos($pClassName, 'Complex\\') !== 0)) {
            
            return false;
        }

        $pClassFilePath = __DIR__ . DIRECTORY_SEPARATOR .
                          'src' . DIRECTORY_SEPARATOR .
                          str_replace(['Complex\\', '\\'], ['', '/'], $pClassName) .
                          '.php';

        if ((file_exists($pClassFilePath) === false) || (is_readable($pClassFilePath) === false)) {
            
            return false;
        }
        require($pClassFilePath);
    }
}
