<?php

namespace Laminas\Loader;

use Laminas\Loader\SplAutoloader;
use Laminas\Loader\StandardAutoloader;
use Traversable;

use function class_exists;
use function is_array;
use function is_subclass_of;
use function spl_autoload_unregister;
use function sprintf;
use function strrchr;
use function substr;

if (class_exists(AutoloaderFactory::class)) {
    return;
}


abstract class AutoloaderFactory
{
    public const STANDARD_AUTOLOADER = StandardAutoloader::class;

    
    protected static $loaders = [];

    
    protected static $standardAutoloader;

    
    public static function factory($options = null)
    {
        if (null === $options) {
            if (! isset(static::$loaders[static::STANDARD_AUTOLOADER])) {
                $autoloader = static::getStandardAutoloader();
                $autoloader->register();
                static::$loaders[static::STANDARD_AUTOLOADER] = $autoloader;
            }

            
            return;
        }

        if (! is_array($options) && ! $options instanceof Traversable) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException(
                'Options provided must be an array or Traversable'
            );
        }

        foreach ($options as $class => $autoloaderOptions) {
            if (! isset(static::$loaders[$class])) {
                $autoloader = static::getStandardAutoloader();
                if (! class_exists($class) && ! $autoloader->autoload($class)) {
                    require_once 'Exception/InvalidArgumentException.php';
                    throw new Exception\InvalidArgumentException(
                        sprintf('Autoloader class "%s" not loaded', $class)
                    );
                }

                if (! is_subclass_of($class, SplAutoloader::class)) {
                    require_once 'Exception/InvalidArgumentException.php';
                    throw new Exception\InvalidArgumentException(
                        sprintf('Autoloader class %s must implement Laminas\\Loader\\SplAutoloader', $class)
                    );
                }

                if ($class === static::STANDARD_AUTOLOADER) {
                    $autoloader->setOptions($autoloaderOptions);
                } else {
                    $autoloader = new $class($autoloaderOptions);
                }
                $autoloader->register();
                static::$loaders[$class] = $autoloader;
            } else {
                static::$loaders[$class]->setOptions($autoloaderOptions);
            }
        }
    }

    
    public static function getRegisteredAutoloaders()
    {
        return static::$loaders;
    }

    
    public static function getRegisteredAutoloader($class)
    {
        if (! isset(static::$loaders[$class])) {
            require_once 'Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException(sprintf('Autoloader class "%s" not loaded', $class));
        }
        return static::$loaders[$class];
    }

    
    public static function unregisterAutoloaders()
    {
        foreach (static::getRegisteredAutoloaders() as $class => $autoloader) {
            spl_autoload_unregister([$autoloader, 'autoload']);
            unset(static::$loaders[$class]);
        }
    }

    
    public static function unregisterAutoloader($autoloaderClass)
    {
        if (! isset(static::$loaders[$autoloaderClass])) {
            return false;
        }

        $autoloader = static::$loaders[$autoloaderClass];
        spl_autoload_unregister([$autoloader, 'autoload']);
        unset(static::$loaders[$autoloaderClass]);
        return true;
    }

    
    protected static function getStandardAutoloader()
    {
        if (null !== static::$standardAutoloader) {
            return static::$standardAutoloader;
        }

        if (! class_exists(static::STANDARD_AUTOLOADER)) {
            
            $stdAutoloader = substr(strrchr(static::STANDARD_AUTOLOADER, '\\'), 1);
            require_once __DIR__ . "/$stdAutoloader.php";
        }
        $loader                     = new StandardAutoloader();
        static::$standardAutoloader = $loader;
        return static::$standardAutoloader;
    }

    
    protected static function isSubclassOf($className, $type)
    {
        return is_subclass_of($className, $type);
    }
}
