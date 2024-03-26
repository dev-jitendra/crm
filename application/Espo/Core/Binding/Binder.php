<?php


namespace Espo\Core\Binding;

use Espo\Core\Binding\Key\NamedClassKey;
use LogicException;
use Closure;

class Binder
{
    public function __construct(private BindingData $data)
    {}

    
    public function bindImplementation(string|NamedClassKey $key, string $implementationClassName): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKey($key);

        $this->data->addGlobal(
            $key,
            Binding::createFromImplementationClassName($implementationClassName)
        );

        return $this;
    }

    
    public function bindService(string|NamedClassKey $key, string $serviceName): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKey($key);

        $this->data->addGlobal(
            $key,
            Binding::createFromServiceName($serviceName)
        );

        return $this;
    }

    
    public function bindCallback(string|NamedClassKey $key, Closure $callback): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKey($key);

        $this->data->addGlobal(
            $key,
            Binding::createFromCallback($callback)
        );

        return $this;
    }

    
    public function bindInstance(string|NamedClassKey $key, object $instance): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKey($key);

        $this->data->addGlobal(
            $key,
            Binding::createFromValue($instance)
        );

        return $this;
    }

    
    public function bindFactory(string|NamedClassKey $key, string $factoryClassName): self
    {
        $key = self::keyToString($key);
        $this->validateBindingKey($key);

        $this->data->addGlobal(
            $key,
            Binding::createFromFactoryClassName($factoryClassName)
        );

        return $this;
    }

    
    public function inContext(string $className, Closure $callback): self
    {
        $contextualBinder = new ContextualBinder($this->data, $className);

        $callback($contextualBinder);

        return $this;
    }

    
    public function for(string $className): ContextualBinder
    {
        return new ContextualBinder($this->data, $className);
    }

    
    private static function keyToString(string|NamedClassKey $key): string
    {
        return is_string($key) ? $key : $key->toString();
    }

    private function validateBindingKey(string $key): void
    {
        if (!$key) {
            throw new LogicException("Bad binding.");
        }

        if ($key[0] === '$') {
            throw new LogicException("Can't binding a parameter name w/o an interface globally.");
        }
    }
}
