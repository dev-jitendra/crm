<?php



namespace Symfony\Component\Routing;

use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Routing\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Exception\RouteCircularReferenceException;


class RouteCollection implements \IteratorAggregate, \Countable
{
    
    private array $routes = [];

    
    private $aliases = [];

    
    private array $resources = [];

    
    private array $priorities = [];

    public function __clone()
    {
        foreach ($this->routes as $name => $route) {
            $this->routes[$name] = clone $route;
        }

        foreach ($this->aliases as $name => $alias) {
            $this->aliases[$name] = clone $alias;
        }
    }

    
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->all());
    }

    
    public function count(): int
    {
        return \count($this->routes);
    }

    public function add(string $name, Route $route, int $priority = 0)
    {
        unset($this->routes[$name], $this->priorities[$name], $this->aliases[$name]);

        $this->routes[$name] = $route;

        if ($priority) {
            $this->priorities[$name] = $priority;
        }
    }

    
    public function all(): array
    {
        if ($this->priorities) {
            $priorities = $this->priorities;
            $keysOrder = array_flip(array_keys($this->routes));
            uksort($this->routes, static function ($n1, $n2) use ($priorities, $keysOrder) {
                return (($priorities[$n2] ?? 0) <=> ($priorities[$n1] ?? 0)) ?: ($keysOrder[$n1] <=> $keysOrder[$n2]);
            });
        }

        return $this->routes;
    }

    
    public function get(string $name): ?Route
    {
        $visited = [];
        while (null !== $alias = $this->aliases[$name] ?? null) {
            if (false !== $searchKey = array_search($name, $visited)) {
                $visited[] = $name;

                throw new RouteCircularReferenceException($name, \array_slice($visited, $searchKey));
            }

            if ($alias->isDeprecated()) {
                $deprecation = $alias->getDeprecation($name);

                trigger_deprecation($deprecation['package'], $deprecation['version'], $deprecation['message']);
            }

            $visited[] = $name;
            $name = $alias->getId();
        }

        return $this->routes[$name] ?? null;
    }

    
    public function remove(string|array $name)
    {
        foreach ((array) $name as $n) {
            unset($this->routes[$n], $this->priorities[$n], $this->aliases[$n]);
        }
    }

    
    public function addCollection(self $collection)
    {
        
        
        foreach ($collection->all() as $name => $route) {
            unset($this->routes[$name], $this->priorities[$name], $this->aliases[$name]);
            $this->routes[$name] = $route;

            if (isset($collection->priorities[$name])) {
                $this->priorities[$name] = $collection->priorities[$name];
            }
        }

        foreach ($collection->getAliases() as $name => $alias) {
            unset($this->routes[$name], $this->priorities[$name], $this->aliases[$name]);

            $this->aliases[$name] = $alias;
        }

        foreach ($collection->getResources() as $resource) {
            $this->addResource($resource);
        }
    }

    
    public function addPrefix(string $prefix, array $defaults = [], array $requirements = [])
    {
        $prefix = trim(trim($prefix), '/');

        if ('' === $prefix) {
            return;
        }

        foreach ($this->routes as $route) {
            $route->setPath('/'.$prefix.$route->getPath());
            $route->addDefaults($defaults);
            $route->addRequirements($requirements);
        }
    }

    
    public function addNamePrefix(string $prefix)
    {
        $prefixedRoutes = [];
        $prefixedPriorities = [];
        $prefixedAliases = [];

        foreach ($this->routes as $name => $route) {
            $prefixedRoutes[$prefix.$name] = $route;
            if (null !== $canonicalName = $route->getDefault('_canonical_route')) {
                $route->setDefault('_canonical_route', $prefix.$canonicalName);
            }
            if (isset($this->priorities[$name])) {
                $prefixedPriorities[$prefix.$name] = $this->priorities[$name];
            }
        }

        foreach ($this->aliases as $name => $alias) {
            $prefixedAliases[$prefix.$name] = $alias->withId($prefix.$alias->getId());
        }

        $this->routes = $prefixedRoutes;
        $this->priorities = $prefixedPriorities;
        $this->aliases = $prefixedAliases;
    }

    
    public function setHost(?string $pattern, array $defaults = [], array $requirements = [])
    {
        foreach ($this->routes as $route) {
            $route->setHost($pattern);
            $route->addDefaults($defaults);
            $route->addRequirements($requirements);
        }
    }

    
    public function setCondition(?string $condition)
    {
        foreach ($this->routes as $route) {
            $route->setCondition($condition);
        }
    }

    
    public function addDefaults(array $defaults)
    {
        if ($defaults) {
            foreach ($this->routes as $route) {
                $route->addDefaults($defaults);
            }
        }
    }

    
    public function addRequirements(array $requirements)
    {
        if ($requirements) {
            foreach ($this->routes as $route) {
                $route->addRequirements($requirements);
            }
        }
    }

    
    public function addOptions(array $options)
    {
        if ($options) {
            foreach ($this->routes as $route) {
                $route->addOptions($options);
            }
        }
    }

    
    public function setSchemes(string|array $schemes)
    {
        foreach ($this->routes as $route) {
            $route->setSchemes($schemes);
        }
    }

    
    public function setMethods(string|array $methods)
    {
        foreach ($this->routes as $route) {
            $route->setMethods($methods);
        }
    }

    
    public function getResources(): array
    {
        return array_values($this->resources);
    }

    
    public function addResource(ResourceInterface $resource)
    {
        $key = (string) $resource;

        if (!isset($this->resources[$key])) {
            $this->resources[$key] = $resource;
        }
    }

    
    public function addAlias(string $name, string $alias): Alias
    {
        if ($name === $alias) {
            throw new InvalidArgumentException(sprintf('Route alias "%s" can not reference itself.', $name));
        }

        unset($this->routes[$name], $this->priorities[$name]);

        return $this->aliases[$name] = new Alias($alias);
    }

    
    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function getAlias(string $name): ?Alias
    {
        return $this->aliases[$name] ?? null;
    }
}
