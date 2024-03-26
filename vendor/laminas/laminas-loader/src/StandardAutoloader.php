<?php

namespace Laminas\Loader;

use Traversable;

use function dirname;
use function file_exists;
use function in_array;
use function is_array;
use function preg_match;
use function rtrim;
use function spl_autoload_register;
use function str_replace;
use function stream_resolve_include_path;
use function strlen;
use function strpos;
use function substr;

use const DIRECTORY_SEPARATOR;


require_once __DIR__ . '/SplAutoloader.php';


class StandardAutoloader implements SplAutoloader
{
    public const NS_SEPARATOR     = '\\';
    public const PREFIX_SEPARATOR = '_';
    public const LOAD_NS          = 'namespaces';
    public const LOAD_PREFIX      = 'prefixes';
    public const ACT_AS_FALLBACK  = 'fallback_autoloader';
    
    public const AUTOREGISTER_ZF      = 'autoregister_laminas';
    public const AUTOREGISTER_LAMINAS = 'autoregister_laminas';

    
    protected $namespaces = [];

    
    protected $prefixes = [];

    
    protected $fallbackAutoloaderFlag = false;

    
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    
    public function setOptions($options)
    {
        if (! is_array($options) && ! $options instanceof Traversable) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Options must be either an array or Traversable');
        }

        foreach ($options as $type => $pairs) {
            switch ($type) {
                case self::AUTOREGISTER_LAMINAS:
                    if ($pairs) {
                        $this->registerNamespace('Laminas', dirname(__DIR__));
                    }
                    break;
                case self::LOAD_NS:
                    if (is_array($pairs) || $pairs instanceof Traversable) {
                        $this->registerNamespaces($pairs);
                    }
                    break;
                case self::LOAD_PREFIX:
                    if (is_array($pairs) || $pairs instanceof Traversable) {
                        $this->registerPrefixes($pairs);
                    }
                    break;
                case self::ACT_AS_FALLBACK:
                    $this->setFallbackAutoloader($pairs);
                    break;
                default:
                    
            }
        }
        return $this;
    }

    
    public function setFallbackAutoloader($flag)
    {
        $this->fallbackAutoloaderFlag = (bool) $flag;
        return $this;
    }

    
    public function isFallbackAutoloader()
    {
        return $this->fallbackAutoloaderFlag;
    }

    
    public function registerNamespace($namespace, $directory)
    {
        $namespace                    = rtrim($namespace, self::NS_SEPARATOR) . self::NS_SEPARATOR;
        $this->namespaces[$namespace] = $this->normalizeDirectory($directory);
        return $this;
    }

    
    public function registerNamespaces($namespaces)
    {
        if (! is_array($namespaces) && ! $namespaces instanceof Traversable) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Namespace pairs must be either an array or Traversable');
        }

        foreach ($namespaces as $namespace => $directory) {
            $this->registerNamespace($namespace, $directory);
        }
        return $this;
    }

    
    public function registerPrefix($prefix, $directory)
    {
        $prefix                  = rtrim($prefix, self::PREFIX_SEPARATOR) . self::PREFIX_SEPARATOR;
        $this->prefixes[$prefix] = $this->normalizeDirectory($directory);
        return $this;
    }

    
    public function registerPrefixes($prefixes)
    {
        if (! is_array($prefixes) && ! $prefixes instanceof Traversable) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException('Prefix pairs must be either an array or Traversable');
        }

        foreach ($prefixes as $prefix => $directory) {
            $this->registerPrefix($prefix, $directory);
        }
        return $this;
    }

    
    public function autoload($class)
    {
        $isFallback = $this->isFallbackAutoloader();
        if (false !== strpos($class, self::NS_SEPARATOR)) {
            if ($this->loadClass($class, self::LOAD_NS)) {
                return $class;
            } elseif ($isFallback) {
                return $this->loadClass($class, self::ACT_AS_FALLBACK);
            }
            return false;
        }
        if (false !== strpos($class, self::PREFIX_SEPARATOR)) {
            if ($this->loadClass($class, self::LOAD_PREFIX)) {
                return $class;
            } elseif ($isFallback) {
                return $this->loadClass($class, self::ACT_AS_FALLBACK);
            }
            return false;
        }
        if ($isFallback) {
            return $this->loadClass($class, self::ACT_AS_FALLBACK);
        }
        return false;
    }

    
    public function register()
    {
        spl_autoload_register([$this, 'autoload']);
    }

    
    protected function transformClassNameToFilename($class, $directory)
    {
        
        
        $matches = [];
        preg_match('/(?P<namespace>.+\\\)?(?P<class>[^\\\]+$)/', $class, $matches);

        $class     = $matches['class'] ?? '';
        $namespace = $matches['namespace'] ?? '';

        return $directory
             . str_replace(self::NS_SEPARATOR, '/', $namespace)
             . str_replace(self::PREFIX_SEPARATOR, '/', $class)
             . '.php';
    }

    
    protected function loadClass($class, $type)
    {
        if (! in_array($type, [self::LOAD_NS, self::LOAD_PREFIX, self::ACT_AS_FALLBACK])) {
            require_once __DIR__ . '/Exception/InvalidArgumentException.php';
            throw new Exception\InvalidArgumentException();
        }

        
        if ($type === self::ACT_AS_FALLBACK) {
            
            $filename     = $this->transformClassNameToFilename($class, '');
            $resolvedName = stream_resolve_include_path($filename);
            if ($resolvedName !== false) {
                return include $resolvedName;
            }
            return false;
        }

        
        foreach ($this->$type as $leader => $path) {
            if (0 === strpos($class, $leader)) {
                
                $trimmedClass = substr($class, strlen($leader));

                
                $filename = $this->transformClassNameToFilename($trimmedClass, $path);
                if (file_exists($filename)) {
                    return include $filename;
                }
            }
        }
        return false;
    }

    
    protected function normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];
        if (in_array($last, ['/', '\\'])) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;
            return $directory;
        }
        $directory .= DIRECTORY_SEPARATOR;
        return $directory;
    }
}
