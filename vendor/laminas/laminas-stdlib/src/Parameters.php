<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

use ArrayObject as PhpArrayObject;
use ReturnTypeWillChange;

use function http_build_query;
use function parse_str;


class Parameters extends PhpArrayObject implements ParametersInterface
{
    
    public function __construct(?array $values = null)
    {
        if (null === $values) {
            $values = [];
        }
        parent::__construct($values, ArrayObject::ARRAY_AS_PROPS);
    }

    
    public function fromArray(array $values)
    {
        $this->exchangeArray($values);
    }

    
    public function fromString($string)
    {
        $array = [];
        parse_str($string, $array);
        $this->fromArray($array);
    }

    
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    
    public function toString()
    {
        return http_build_query($this->toArray());
    }

    
    #[ReturnTypeWillChange]
    public function offsetGet($name)
    {
        if ($this->offsetExists($name)) {
            return parent::offsetGet($name);
        }

        return null;
    }

    
    public function get($name, $default = null)
    {
        if ($this->offsetExists($name)) {
            return parent::offsetGet($name);
        }
        return $default;
    }

    
    public function set($name, $value)
    {
        $this[$name] = $value;
        return $this;
    }
}
