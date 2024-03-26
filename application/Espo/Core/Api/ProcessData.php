<?php


namespace Espo\Core\Api;

class ProcessData
{
    
    public function __construct(
        private Route $route,
        private string $basePath,
        private array $routeParams
    ) {}

    
    public function getRoute(): Route
    {
        return $this->route;
    }

    
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }
}
