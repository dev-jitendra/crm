<?php


namespace Espo\Core\Binding;

use Closure;
use Espo\Core\Binding\Key\NamedClassKey;
use Espo\Core\Binding\Key\NamedKey;
use LogicException;

class ContextualBinder
{
    private BindingData $data;
    
    private string $className;

    
    public function __construct(BindingData $data, string $className)
    {
        $this->data = $data;
        $this->className = $className;
    }

    
    public function bindImplementation(string|NamedClassKey $key, string $implementationClassName): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKeyNoParameterName($key);

        $this->data->addContext(
            $this->className,
            $key,
            Binding::createFromImplementationClassName($implementationClassName)
        );

        return $this;
    }

    
    public function bindService(string|NamedClassKey $key, string $serviceName): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKeyNoParameterName($key);

        $this->data->addContext(
            $this->className,
            $key,
            Binding::createFromServiceName($serviceName)
        );

        return $this;
    }

    
    public function bindValue(string|NamedKey|NamedClassKey $key, $value): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKeyParameterName($key);

        $this->data->addContext(
            $this->className,
            $key,
            Binding::createFromValue($value)
        );

        return $this;
    }

    
    public function bindInstance(string|NamedClassKey $key, object $instance): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKeyNoParameterName($key);

        $this->data->addContext(
            $this->className,
            $key,
            Binding::createFromValue($instance)
        );

        return $this;
    }

    
    public function bindCallback(string|NamedClassKey|NamedKey $key, Closure $callback): self
    {
        $key = self::keyToString($key);
        $this->validateBinding($key);

        $this->data->addContext(
            $this->className,
            $key,
            Binding::createFromCallback($callback)
        );

        return $this;
    }

    
    public function bindFactory(string|NamedClassKey $key, string $factoryClassName): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKeyNoParameterName($key);

        $this->data->addContext(
            $this->className,
            $key,
            Binding::createFromFactoryClassName($factoryClassName)
        );

        return $this;
    }

    private function validateBinding(string $key): void
    {
        if (!$key) {
            throw new LogicException("Bad binding.");
        }
    }

    private function validateBindingKeyNoParameterName(string $key): void
    {
        $this->validateBinding($key);

        if ($key[0] === '$') {
            throw new LogicException("Can't bind a parameter name w/o an interface.");
        }
    }

    private function validateBindingKeyParameterName(string $key): void
    {
        $this->validateBinding($key);

        if (!str_contains($key, '$')) {
            throw new LogicException("Can't bind w/o a parameter name.");
        }
    }

    
    private static function keyToString(string|NamedKey|NamedClassKey $key): string
    {
        return is_string($key) ? $key : $key->toString();
    }
}
