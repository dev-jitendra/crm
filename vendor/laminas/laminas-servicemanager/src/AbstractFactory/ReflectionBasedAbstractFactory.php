<?php

declare(strict_types=1);

namespace Laminas\ServiceManager\AbstractFactory;

use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

use function array_map;
use function class_exists;
use function interface_exists;
use function is_string;
use function sprintf;


class ReflectionBasedAbstractFactory implements AbstractFactoryInterface
{
    
    protected $aliases = [];

    
    public function __construct(array $aliases = [])
    {
        if (! empty($aliases)) {
            $this->aliases = $aliases;
        }
    }

    
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $reflectionClass = new ReflectionClass($requestedName);

        if (null === ($constructor = $reflectionClass->getConstructor())) {
            return new $requestedName();
        }

        $reflectionParameters = $constructor->getParameters();

        if (empty($reflectionParameters)) {
            return new $requestedName();
        }

        $resolver = $container->has('config')
            ? $this->resolveParameterWithConfigService($container, $requestedName)
            : $this->resolveParameterWithoutConfigService($container, $requestedName);

        $parameters = array_map($resolver, $reflectionParameters);

        return new $requestedName(...$parameters);
    }

    
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return class_exists($requestedName) && $this->canCallConstructor($requestedName);
    }

    private function canCallConstructor(string $requestedName): bool
    {
        $constructor = (new ReflectionClass($requestedName))->getConstructor();

        return $constructor === null || $constructor->isPublic();
    }

    
    private function resolveParameterWithoutConfigService(ContainerInterface $container, $requestedName)
    {
        
        return fn(ReflectionParameter $parameter) => $this->resolveParameter($parameter, $container, $requestedName);
    }

    
    private function resolveParameterWithConfigService(ContainerInterface $container, $requestedName)
    {
        
        return function (ReflectionParameter $parameter) use ($container, $requestedName) {
            if ($parameter->getName() === 'config') {
                $type = $parameter->getType();
                if ($type instanceof ReflectionNamedType && $type->getName() === 'array') {
                    return $container->get('config');
                }
            }
            return $this->resolveParameter($parameter, $container, $requestedName);
        };
    }

    
    private function resolveParameter(ReflectionParameter $parameter, ContainerInterface $container, $requestedName)
    {
        $type = $parameter->getType();
        $type = $type instanceof ReflectionNamedType ? $type->getName() : null;

        if ($type === 'array') {
            return [];
        }

        if ($type === null || (is_string($type) && ! class_exists($type) && ! interface_exists($type))) {
            if (! $parameter->isDefaultValueAvailable()) {
                throw new ServiceNotFoundException(sprintf(
                    'Unable to create service "%s"; unable to resolve parameter "%s" '
                    . 'to a class, interface, or array type',
                    $requestedName,
                    $parameter->getName()
                ));
            }

            return $parameter->getDefaultValue();
        }

        $type = $this->aliases[$type] ?? $type;

        if ($container->has($type)) {
            return $container->get($type);
        }

        if (! $parameter->isOptional()) {
            throw new ServiceNotFoundException(sprintf(
                'Unable to create service "%s"; unable to resolve parameter "%s" using type hint "%s"',
                $requestedName,
                $parameter->getName(),
                $type
            ));
        }

        
        
        return $parameter->getDefaultValue();
    }
}
