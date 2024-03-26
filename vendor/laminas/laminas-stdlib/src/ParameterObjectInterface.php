<?php

declare(strict_types=1);

namespace Laminas\Stdlib;


interface ParameterObjectInterface
{
    
    public function __set($key, mixed $value);

    
    public function __get($key);

    
    public function __isset($key);

    
    public function __unset($key);
}
