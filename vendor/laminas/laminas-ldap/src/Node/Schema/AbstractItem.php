<?php

namespace Laminas\Ldap\Node\Schema;

use ArrayAccess;
use Countable;
use Laminas\Ldap\Exception;
use Laminas\Ldap\Exception\BadMethodCallException;
use ReturnTypeWillChange;

use function array_key_exists;
use function count;


abstract class AbstractItem implements ArrayAccess, Countable
{
    
    protected $data;

    
    public function __construct(array $data)
    {
        $this->setData($data);
    }

    
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    
    public function getData()
    {
        return $this->data;
    }

    
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new Exception\BadMethodCallException();
    }

    
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new Exception\BadMethodCallException();
    }

    
    #[ReturnTypeWillChange]
    public function offsetExists($name)
    {
        return $this->__isset($name);
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->data);
    }
}
