<?php


namespace Espo\Core\Binding;

use LogicException;
use stdClass;

class BindingData
{
    private stdClass $global;
    private stdClass $context;

    public function __construct()
    {
        $this->global = (object) [];
        $this->context = (object) [];
    }

    public function addContext(string $className, string $key, Binding $binding): void
    {
        if (!property_exists($this->context, $className)) {
            $this->context->$className = (object) [];
        }

        $this->context->$className->$key = $binding;
    }

    public function addGlobal(string $key, Binding $binding): void
    {
        $this->global->$key = $binding;
    }

    
    public function hasContext(string $className, string $key): bool
    {
        if (!property_exists($this->context, $className)) {
            return false;
        }

        if (!property_exists($this->context->$className, $key)) {
            return false;
        }

        return true;
    }

    
    public function getContext(string $className, string $key): Binding
    {
        if (!$this->hasContext($className, $key)) {
            throw new LogicException("No data.");
        }

        return $this->context->$className->$key;
    }

    public function hasGlobal(string $key): bool
    {
        if (!property_exists($this->global, $key)) {
            return false;
        }

        return true;
    }

    public function getGlobal(string $key): Binding
    {
        if (!$this->hasGlobal($key)) {
            throw new LogicException("No data.");
        }

        return $this->global->$key;
    }

    
    public function getGlobalKeyList(): array
    {
        return array_keys(
            get_object_vars($this->global)
        );
    }

    
    public function getContextList(): array
    {
        
        return array_keys(
            get_object_vars($this->context)
        );
    }

    
    public function getContextKeyList(string $context): array
    {
        return array_keys(
            get_object_vars($this->context->$context ?? (object) [])
        );
    }
}
