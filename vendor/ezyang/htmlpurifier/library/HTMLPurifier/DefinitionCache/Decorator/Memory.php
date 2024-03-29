<?php


class HTMLPurifier_DefinitionCache_Decorator_Memory extends HTMLPurifier_DefinitionCache_Decorator
{
    
    protected $definitions;

    
    public $name = 'Memory';

    
    public function copy()
    {
        return new HTMLPurifier_DefinitionCache_Decorator_Memory();
    }

    
    public function add($def, $config)
    {
        $status = parent::add($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }
        return $status;
    }

    
    public function set($def, $config)
    {
        $status = parent::set($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }
        return $status;
    }

    
    public function replace($def, $config)
    {
        $status = parent::replace($def, $config);
        if ($status) {
            $this->definitions[$this->generateKey($config)] = $def;
        }
        return $status;
    }

    
    public function get($config)
    {
        $key = $this->generateKey($config);
        if (isset($this->definitions[$key])) {
            return $this->definitions[$key];
        }
        $this->definitions[$key] = parent::get($config);
        return $this->definitions[$key];
    }
}


