<?php

namespace FastRoute;

class RouteCollector
{
    
    protected $routeParser;

    
    protected $dataGenerator;

    
    protected $currentGroupPrefix;

    
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator)
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->currentGroupPrefix = '';
    }

    
    public function addRoute($httpMethod, $route, $handler)
    {
        $route = $this->currentGroupPrefix . $route;
        $routeDatas = $this->routeParser->parse($route);
        foreach ((array) $httpMethod as $method) {
            foreach ($routeDatas as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, $handler);
            }
        }
    }

    
    public function addGroup($prefix, callable $callback)
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        $callback($this);
        $this->currentGroupPrefix = $previousGroupPrefix;
    }

    
    public function get($route, $handler)
    {
        $this->addRoute('GET', $route, $handler);
    }

    
    public function post($route, $handler)
    {
        $this->addRoute('POST', $route, $handler);
    }

    
    public function put($route, $handler)
    {
        $this->addRoute('PUT', $route, $handler);
    }

    
    public function delete($route, $handler)
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    
    public function patch($route, $handler)
    {
        $this->addRoute('PATCH', $route, $handler);
    }

    
    public function head($route, $handler)
    {
        $this->addRoute('HEAD', $route, $handler);
    }

    
    public function getData()
    {
        return $this->dataGenerator->getData();
    }
}
