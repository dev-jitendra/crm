<?php


namespace Espo\Core;

use Psr\Container\NotFoundExceptionInterface;

use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\Binding;
use Espo\Core\Binding\Factory;
use Espo\Core\Interfaces\Injectable;

use ReflectionClass;
use ReflectionParameter;
use ReflectionFunction;
use ReflectionNamedType;
use Throwable;
use RuntimeException;
use Closure;


class InjectableFactory
{
    public function __construct(
        private Container $container,
        private ?BindingContainer $bindingContainer = null
    ) {}

    
    public function create(string $className): object
    {
        return $this->createInternal($className);
    }

    
    public function createWith(string $className, array $with): object
    {
        return $this->createInternal($className, $with);
    }

    
    public function createWithBinding(string $className, BindingContainer $bindingContainer): object
    {
        return $this->createInternal($className, null, $bindingContainer);
    }

    
    public function createResolved(string $interfaceName, ?BindingContainer $bindingContainer = null): object
    {
        $binding = $this->bindingContainer && $this->bindingContainer->hasByInterface($interfaceName) ?
            $this->bindingContainer->getByInterface($interfaceName) :
            null;

        if (!$binding) {
            $class = new ReflectionClass($interfaceName);

            if ($class->isInterface()) {
                throw new RuntimeException("Could not resolve interface `$interfaceName`.");
            }

            $obj = $this->createInternal($interfaceName, null, $bindingContainer);

            if (!$obj instanceof $interfaceName) {
                throw new RuntimeException("Class `$interfaceName` resolved to another type.");
            }

            return $obj;
        }

        $typeList = [
            Binding::IMPLEMENTATION_CLASS_NAME,
            Binding::FACTORY_CLASS_NAME,
            Binding::CALLBACK,
        ];

        if (!in_array($binding->getType(), $typeList)) {
            throw new RuntimeException("Bad resolution for interface `$interfaceName`.");
        }

        $obj = $this->resolveBinding($binding, $bindingContainer);

        if (!$obj instanceof $interfaceName) {
            throw new RuntimeException("Class `$interfaceName` resolved to another type.");
        }

        return $obj;
    }

    
    private function createInternal(
        string $className,
        ?array $with = null,
        ?BindingContainer $bindingContainer = null
    ): object {

        if (!class_exists($className)) {
            throw new RuntimeException("InjectableFactory: Class '$className' does not exist.");
        }

        $class = new ReflectionClass($className);

        $injectionList = $this->getConstructorInjectionList($class, $with, $bindingContainer);

        $obj = $class->newInstanceArgs($injectionList);

        
        if ($class->implementsInterface(Injectable::class)) {
            $this->applyInjectable($class, $obj);

            return $obj;
        }

        $this->applyAwareInjections($class, $obj);

        return $obj;
    }

    
    private function getConstructorInjectionList(
        ReflectionClass $class,
        ?array $with = null,
        ?BindingContainer $bindingContainer = null
    ): array {

        $injectionList = [];

        $constructor = $class->getConstructor();

        if (!$constructor) {
            return $injectionList;
        }

        $params = $constructor->getParameters();

        foreach ($params as $param) {
            $injectionList[] = $this->getMethodParamInjection($class, $param, $with, $bindingContainer);
        }

        return $injectionList;
    }

    
    private function getMethodParamInjection(
        ?ReflectionClass $class,
        ReflectionParameter $param,
        ?array $with = null,
        ?BindingContainer $bindingContainer = null
    ) {

        $name = $param->getName();

        if ($with && array_key_exists($name, $with)) {
            return $with[$name];
        }

        $dependencyClass = null;

        $type = $param->getType();

        if (
            $type &&
            $type instanceof ReflectionNamedType &&
            !$type->isBuiltin()
        ) {
            try {
                
                $dependencyClassName = $type->getName();

                $dependencyClass = new ReflectionClass($dependencyClassName);
            }
            catch (Throwable $e) {
                $badClassName = $type->getName();

                
                class_exists($badClassName);

                throw new RuntimeException("InjectableFactory: " . $e->getMessage());
            }
        }

        if ($bindingContainer && $bindingContainer->hasByParam($class, $param)) {
            $binding = $bindingContainer->getByParam($class, $param);

            return $this->resolveBinding($binding, $bindingContainer);
        }

        if ($this->bindingContainer && $this->bindingContainer->hasByParam($class, $param)) {
            $binding = $this->bindingContainer->getByParam($class, $param);

            return $this->resolveBinding($binding, $bindingContainer);
        }

        if (!$dependencyClass && $param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        if (
            $dependencyClass && $this->container->has($name) &&
            $this->areDependencyClassesMatching($dependencyClass, $this->container->getClass($name))
        ) {
            return $this->container->get($name);
        }

        if ($dependencyClass && $param->allowsNull()) {
            return null;
        }

        if ($dependencyClass) {
            return $this->createInternal($dependencyClass->getName(), null, $bindingContainer);
        }

        if (!$class) {
            throw new RuntimeException(
                "InjectableFactory: Could not resolve the dependency '$name' for a callback."
            );
        }

        $className = $class->getName();

        throw new RuntimeException(
            "InjectableFactory: Could not create '$className', the dependency '$name' is not resolved."
        );
    }

    
    private function getCallbackInjectionList(callable $callback): array
    {
        $injectionList = [];

        if (!$callback instanceof Closure) {
            $callback = Closure::fromCallable($callback);
        }

        $function = new ReflectionFunction($callback);

        foreach ($function->getParameters() as $param) {
            $injectionList[] = $this->getMethodParamInjection(null, $param);
        }

        return $injectionList;
    }

    private function resolveBinding(Binding $binding, ?BindingContainer $bindingContainer): mixed
    {
        $type = $binding->getType();
        $value = $binding->getValue();

        if ($type === Binding::CONTAINER_SERVICE) {
            try {
                return $this->container->get($value);
            }
            catch (NotFoundExceptionInterface $e) {
                throw new RuntimeException($e->getMessage());
            }
        }

        if ($type === Binding::IMPLEMENTATION_CLASS_NAME) {
            
            return $this->createInternal($value, null, $bindingContainer);
        }

        if ($type === Binding::VALUE) {
            return $value;
        }

        if ($type === Binding::CALLBACK) {
            $callback = $value;

            $dependencyList = $this->getCallbackInjectionList($callback);

            return $callback(...$dependencyList);
        }

        if ($type === Binding::FACTORY_CLASS_NAME) {
            
            
            $factory = $this->createInternal($value, null, $bindingContainer);

            return $factory->create();
        }

        throw new RuntimeException("InjectableFactory: Bad binding.");
    }

    
    private function areDependencyClassesMatching(
        ReflectionClass $paramHintClass,
        ReflectionClass $returnHintClass
    ): bool {

        if ($paramHintClass->getName() === $returnHintClass->getName()) {
            return true;
        }

        if ($returnHintClass->isSubclassOf($paramHintClass)) {
            return true;
        }

        return false;
    }

    
    private function applyAwareInjections(ReflectionClass $class, object $obj, array $ignoreList = []): void
    {
        foreach ($class->getInterfaces() as $interface) {
            $interfaceName = $interface->getShortName();

            if (!str_ends_with($interfaceName, 'Aware') || strlen($interfaceName) <= 5) {
                continue;
            }

            $name = lcfirst(substr($interfaceName, 0, -5));

            if (in_array($name, $ignoreList)) {
                continue;
            }

            if (!$this->classHasDependencySetter($class, $name, true)) {
                continue;
            }

            $injection = $this->container->get($name);

            $methodName = 'set' . ucfirst($name);

            $obj->$methodName($injection);
        }
    }

    
    private function classHasDependencySetter(
        ReflectionClass $class,
        string $name,
        bool $skipInstanceCheck = false
    ): bool {

        $methodName = 'set' . ucfirst($name);

        if (!$class->hasMethod($methodName) || !$class->getMethod($methodName)->isPublic()) {
            return false;
        }

        $params = $class->getMethod($methodName)->getParameters();

        if (!count($params)) {
            return false;
        }

        if ($skipInstanceCheck) {
            return true;
        }

        $injection = $this->container->get($name);

        $paramClass = null;

        $type = $params[0]->getType();

        if (
            $type &&
            $type instanceof ReflectionNamedType &&
            !$type->isBuiltin()
        ) {
            
            $dependencyClassName = $type->getName();

            $paramClass = new ReflectionClass($dependencyClassName);
        }

        if ($paramClass && $paramClass->isInstance($injection)) {
            return true;
        }

        return false;
    }

    
    public function createByClassName(string $className, ?array $with = null): object
    {
        return $this->createInternal($className, $with);
    }

    
    private function applyInjectable(ReflectionClass $class, object $obj): void
    {
        $setList = [];

        assert($obj instanceof Injectable);

        $dependencyList = $obj->getDependencyList();

        foreach ($dependencyList as $name) {
            $injection = $this->container->get($name);

            if ($this->classHasDependencySetter($class, $name)) {
                $methodName = 'set' . ucfirst($name);
                $obj->$methodName($injection);
                $setList[] = $name;
            }

            $obj->inject($name, $injection);
        }

        $this->applyAwareInjections($class, $obj, $setList);
    }
}
