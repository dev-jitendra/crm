<?php


namespace Espo\Core\Binding;

use Closure;
use Espo\Core\Binding\Key\NamedClassKey;

class BindingContainerBuilder
{
    private BindingData $data;
    private Binder $binder;

    public function __construct()
    {
        $this->data = new BindingData();
        $this->binder = new Binder($this->data);
    }

    
    public function bindImplementation(string|NamedClassKey $key, string $implementationClassName): self
    {
        $this->binder->bindImplementation($key, $implementationClassName);

        return $this;
    }

    
    public function bindService(string|NamedClassKey $key, string $serviceName): self
    {
        $this->binder->bindService($key, $serviceName);

        return $this;
    }

    
    public function bindCallback(string|NamedClassKey $key, Closure $callback): self
    {
        $this->binder->bindCallback($key, $callback);

        return $this;
    }

    
    public function bindInstance(string|NamedClassKey $key, object $instance): self
    {
        $this->binder->bindInstance($key, $instance);

        return $this;
    }

    
    public function bindFactory(string|NamedClassKey $key, string $factoryClassName): self
    {
        $this->binder->bindFactory($key, $factoryClassName);

        return $this;
    }

    
    public function inContext(string $className, Closure $callback): self
    {
        $contextualBinder = new ContextualBinder($this->data, $className);

        $callback($contextualBinder);

        return $this;
    }

    
    public function build(): BindingContainer
    {
        return new BindingContainer($this->data);
    }

    
    public static function create(): self
    {
        return new self();
    }
}
