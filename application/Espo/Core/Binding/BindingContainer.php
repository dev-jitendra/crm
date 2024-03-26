<?php


namespace Espo\Core\Binding;

use ReflectionClass;
use ReflectionParameter;
use ReflectionNamedType;
use LogicException;


class BindingContainer
{
    public function __construct(private BindingData $data)
    {}

    
    public function hasByParam(?ReflectionClass $class, ReflectionParameter $param): bool
    {
        if ($this->getInternal($class, $param) === null) {
            return false;
        }

        return true;
    }

    
    public function getByParam(?ReflectionClass $class, ReflectionParameter $param): Binding
    {
        if (!$this->hasByParam($class, $param)) {
            throw new LogicException("Cannot get not existing binding.");
        }

        
        return $this->getInternal($class, $param);
    }

    
    public function hasByInterface(string $interfaceName): bool
    {
        return $this->data->hasGlobal($interfaceName);
    }

    
    public function getByInterface(string $interfaceName): Binding
    {
        if (!$this->hasByInterface($interfaceName)) {
            throw new LogicException("Binding for interface `{$interfaceName}` does not exist.");
        }

        if (!interface_exists($interfaceName) && !class_exists($interfaceName)) {
            throw new LogicException("Interface `{$interfaceName}` does not exist.");
        }

        return $this->data->getGlobal($interfaceName);
    }

    
    private function getInternal(?ReflectionClass $class, ReflectionParameter $param): ?Binding
    {
        $className = null;

        $key = null;

        if ($class) {
            $className = $class->getName();

            $key = '$' . $param->getName();
        }

        $type = $param->getType();

        if (
            $className &&
            $key &&
            $this->data->hasContext($className, $key)
        ) {
            $binding = $this->data->getContext($className, $key);

            $notMatching =
                $type instanceof ReflectionNamedType &&
                !$type->isBuiltin() &&
                $binding->getType() === Binding::VALUE &&
                is_scalar($binding->getValue());

            if (!$notMatching) {
                return $binding;
            }
        }

        $dependencyClassName = null;

        if (
            $type instanceof ReflectionNamedType &&
            !$type->isBuiltin()
        ) {
            $dependencyClassName = $type->getName();
        }

        $key = null;
        $keyWithParamName = null;

        if ($dependencyClassName) {
            $key = $dependencyClassName;

            $keyWithParamName = $key . ' $' . $param->getName();
        }

        if ($keyWithParamName) {
            if ($className && $this->data->hasContext($className, $keyWithParamName)) {
                return $this->data->getContext($className, $keyWithParamName);
            }

            if ($this->data->hasGlobal($keyWithParamName)) {
                return $this->data->getGlobal($keyWithParamName);
            }
        }

        if ($key) {
            if ($className && $this->data->hasContext($className, $key)) {
                return $this->data->getContext($className, $key);
            }

            if ($this->data->hasGlobal($key)) {
                return $this->data->getGlobal($key);
            }
        }

        return null;
    }
}
