<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use AllowDynamicProperties;
use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use ReturnTypeWillChange;
use Serializable;
use UnexpectedValueException;

use function array_key_exists;
use function array_keys;
use function asort;
use function class_exists;
use function count;
use function get_debug_type;
use function get_object_vars;
use function gettype;
use function in_array;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function ksort;
use function natcasesort;
use function natsort;
use function serialize;
use function sprintf;
use function str_starts_with;
use function uasort;
use function uksort;
use function unserialize;


#[AllowDynamicProperties]
class ArrayObject implements IteratorAggregate, ArrayAccess, Serializable, Countable
{
    
    public const STD_PROP_LIST = 1;

    
    public const ARRAY_AS_PROPS = 2;

    
    protected $storage;

    
    protected $flag;

    
    protected $iteratorClass;

    
    protected $protectedProperties;

    
    public function __construct($input = [], $flags = self::STD_PROP_LIST, $iteratorClass = ArrayIterator::class)
    {
        $this->setFlags($flags);
        $this->storage = $input;
        $this->setIteratorClass($iteratorClass);
        $this->protectedProperties = array_keys(get_object_vars($this));
    }

    
    public function __isset(mixed $key)
    {
        if ($this->flag === self::ARRAY_AS_PROPS) {
            return $this->offsetExists($key);
        }

        if (in_array($key, $this->protectedProperties)) {
            throw new Exception\InvalidArgumentException("$key is a protected property, use a different key");
        }

        return isset($this->$key);
    }

    
    public function __set(mixed $key, mixed $value)
    {
        if ($this->flag === self::ARRAY_AS_PROPS) {
            $this->offsetSet($key, $value);
            return;
        }

        if (in_array($key, $this->protectedProperties)) {
            throw new Exception\InvalidArgumentException("$key is a protected property, use a different key");
        }

        $this->$key = $value;
    }

    
    public function __unset(mixed $key)
    {
        if ($this->flag === self::ARRAY_AS_PROPS) {
            $this->offsetUnset($key);
            return;
        }

        if (in_array($key, $this->protectedProperties)) {
            throw new Exception\InvalidArgumentException("$key is a protected property, use a different key");
        }

        unset($this->$key);
    }

    
    public function &__get(mixed $key)
    {
        if ($this->flag === self::ARRAY_AS_PROPS) {
            $ret = &$this->offsetGet($key);

            return $ret;
        }

        if (in_array($key, $this->protectedProperties, true)) {
            throw new Exception\InvalidArgumentException("$key is a protected property, use a different key");
        }

        return $this->$key;
    }

    
    public function append(mixed $value)
    {
        $this->storage[] = $value;
    }

    
    public function asort()
    {
        asort($this->storage);
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->storage);
    }

    
    public function exchangeArray($data)
    {
        if (! is_array($data) && ! is_object($data)) {
            throw new Exception\InvalidArgumentException(
                'Passed variable is not an array or object, using empty array instead'
            );
        }

        if (is_object($data) && ($data instanceof self || $data instanceof \ArrayObject)) {
            $data = $data->getArrayCopy();
        }
        if (! is_array($data)) {
            $data = (array) $data;
        }

        $storage = $this->storage;

        $this->storage = $data;

        return $storage;
    }

    
    public function getArrayCopy()
    {
        return $this->storage;
    }

    
    public function getFlags()
    {
        return $this->flag;
    }

    
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $class = $this->iteratorClass;

        return new $class($this->storage);
    }

    
    public function getIteratorClass()
    {
        return $this->iteratorClass;
    }

    
    public function ksort()
    {
        ksort($this->storage);
    }

    
    public function natcasesort()
    {
        natcasesort($this->storage);
    }

    
    public function natsort()
    {
        natsort($this->storage);
    }

    
    #[ReturnTypeWillChange]
    public function offsetExists(mixed $key)
    {
        return isset($this->storage[$key]);
    }

    
    #[ReturnTypeWillChange]
    public function &offsetGet(mixed $key)
    {
        $ret = null;
        if (! $this->offsetExists($key)) {
            return $ret;
        }
        $ret = &$this->storage[$key];

        return $ret;
    }

    
    #[ReturnTypeWillChange]
    public function offsetSet(mixed $key, mixed $value)
    {
        $this->storage[$key] = $value;
    }

    
    #[ReturnTypeWillChange]
    public function offsetUnset(mixed $key)
    {
        if ($this->offsetExists($key)) {
            unset($this->storage[$key]);
        }
    }

    
    public function serialize()
    {
        return serialize($this->__serialize());
    }

    
    public function __serialize()
    {
        return get_object_vars($this);
    }

    
    public function setFlags($flags)
    {
        $this->flag = $flags;
    }

    
    public function setIteratorClass($class)
    {
        if (class_exists($class)) {
            $this->iteratorClass = $class;

            return;
        }

        if (str_starts_with($class, '\\')) {
            $class = '\\' . $class;
            if (class_exists($class)) {
                $this->iteratorClass = $class;

                return;
            }
        }

        throw new Exception\InvalidArgumentException('The iterator class does not exist');
    }

    
    public function uasort($function)
    {
        if (is_callable($function)) {
            uasort($this->storage, $function);
        }
    }

    
    public function uksort($function)
    {
        if (is_callable($function)) {
            uksort($this->storage, $function);
        }
    }

    
    public function unserialize($data)
    {
        $toUnserialize = unserialize($data);
        if (! is_array($toUnserialize)) {
            throw new UnexpectedValueException(sprintf(
                'Cannot deserialize %s instance; corrupt serialization data',
                self::class
            ));
        }

        $this->__unserialize($toUnserialize);
    }

    
    public function __unserialize($data)
    {
        $this->protectedProperties = array_keys(get_object_vars($this));

        
        if (array_key_exists('flag', $data)) {
            $this->setFlags((int) $data['flag']);
            unset($data['flag']);
        }

        if (array_key_exists('storage', $data)) {
            if (! is_array($data['storage']) && ! is_object($data['storage'])) {
                throw new UnexpectedValueException(sprintf(
                    'Cannot deserialize %s instance: corrupt storage data; expected array or object, received %s',
                    self::class,
                    gettype($data['storage'])
                ));
            }
            $this->exchangeArray($data['storage']);
            unset($data['storage']);
        }

        if (array_key_exists('iteratorClass', $data)) {
            if (! is_string($data['iteratorClass'])) {
                throw new UnexpectedValueException(sprintf(
                    'Cannot deserialize %s instance: invalid iteratorClass; expected string, received %s',
                    self::class,
                    get_debug_type($data['iteratorClass'])
                ));
            }
            $this->setIteratorClass($data['iteratorClass']);
            unset($data['iteratorClass']);
        }

        unset($data['protectedProperties']);

        
        foreach ($data as $k => $v) {
            $this->__set($k, $v);
        }
    }
}
