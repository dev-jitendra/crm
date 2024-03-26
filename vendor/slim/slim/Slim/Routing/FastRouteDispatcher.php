<?php



declare(strict_types=1);

namespace Slim\Routing;

use FastRoute\Dispatcher\GroupCountBased;

class FastRouteDispatcher extends GroupCountBased
{
    
    private array $allowedMethods = [];

    
    public function dispatch($httpMethod, $uri): array
    {
        $routingResults = $this->routingResults($httpMethod, $uri);
        if ($routingResults[0] === self::FOUND) {
            return $routingResults;
        }

        
        if ($httpMethod === 'HEAD') {
            $routingResults = $this->routingResults('GET', $uri);
            if ($routingResults[0] === self::FOUND) {
                return $routingResults;
            }
        }

        
        $routingResults = $this->routingResults('*', $uri);
        if ($routingResults[0] === self::FOUND) {
            return $routingResults;
        }

        if (!empty($this->getAllowedMethods($uri))) {
            return [self::METHOD_NOT_ALLOWED, null, []];
        }

        return [self::NOT_FOUND, null, []];
    }

    
    private function routingResults(string $httpMethod, string $uri): array
    {
        if (isset($this->staticRouteMap[$httpMethod][$uri])) {
            
            $routeIdentifier = $this->staticRouteMap[$httpMethod][$uri];
            return [self::FOUND, $routeIdentifier, []];
        }

        if (isset($this->variableRouteData[$httpMethod])) {
            
            $result = $this->dispatchVariableRoute($this->variableRouteData[$httpMethod], $uri);
            if ($result[0] === self::FOUND) {
                
                return [self::FOUND, $result[1], $result[2]];
            }
        }

        return [self::NOT_FOUND, null, []];
    }

    
    public function getAllowedMethods(string $uri): array
    {
        if (isset($this->allowedMethods[$uri])) {
            return $this->allowedMethods[$uri];
        }

        $allowedMethods = [];
        foreach ($this->staticRouteMap as $method => $uriMap) {
            if (isset($uriMap[$uri])) {
                $allowedMethods[$method] = true;
            }
        }

        foreach ($this->variableRouteData as $method => $routeData) {
            $result = $this->dispatchVariableRoute($routeData, $uri);
            if ($result[0] === self::FOUND) {
                $allowedMethods[$method] = true;
            }
        }

        return $this->allowedMethods[$uri] = array_keys($allowedMethods);
    }
}
