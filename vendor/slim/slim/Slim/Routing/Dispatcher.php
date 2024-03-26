<?php

declare(strict_types=1);

namespace Slim\Routing;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector as FastRouteCollector;
use FastRoute\RouteParser\Std;
use Slim\Interfaces\DispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;

class Dispatcher implements DispatcherInterface
{
    private RouteCollectorInterface $routeCollector;

    private ?FastRouteDispatcher $dispatcher = null;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    protected function createDispatcher(): FastRouteDispatcher
    {
        if ($this->dispatcher) {
            return $this->dispatcher;
        }

        $routeDefinitionCallback = function (FastRouteCollector $r): void {
            $basePath = $this->routeCollector->getBasePath();

            foreach ($this->routeCollector->getRoutes() as $route) {
                $r->addRoute($route->getMethods(), $basePath . $route->getPattern(), $route->getIdentifier());
            }
        };

        $cacheFile = $this->routeCollector->getCacheFile();
        if ($cacheFile) {
            
            $dispatcher = \FastRoute\cachedDispatcher($routeDefinitionCallback, [
                'dataGenerator' => GroupCountBased::class,
                'dispatcher' => FastRouteDispatcher::class,
                'routeParser' => new Std(),
                'cacheFile' => $cacheFile,
            ]);
        } else {
            
            $dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback, [
                'dataGenerator' => GroupCountBased::class,
                'dispatcher' => FastRouteDispatcher::class,
                'routeParser' => new Std(),
            ]);
        }

        $this->dispatcher = $dispatcher;
        return $this->dispatcher;
    }

    
    public function dispatch(string $method, string $uri): RoutingResults
    {
        $dispatcher = $this->createDispatcher();
        $results = $dispatcher->dispatch($method, $uri);
        return new RoutingResults($this, $method, $uri, $results[0], $results[1], $results[2]);
    }

    
    public function getAllowedMethods(string $uri): array
    {
        $dispatcher = $this->createDispatcher();
        return $dispatcher->getAllowedMethods($uri);
    }
}
