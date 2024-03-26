<?php


namespace Espo\Core\Api;

class Route
{
    private string $method;

    
    public function __construct(
        string $method,
        private string $route,
        private string $adjustedRoute,
        private array $params,
        private bool $noAuth,
        private ?string $actionClassName
    ) {
        $this->method = strtoupper($method);
    }

    
    public function getActionClassName(): ?string
    {
        return $this->actionClassName;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    
    public function getRoute(): string
    {
        return $this->route;
    }

    
    public function getAdjustedRoute(): string
    {
        return $this->adjustedRoute;
    }

    
    public function getParams(): array
    {
        return $this->params;
    }

    public function noAuth(): bool
    {
        return $this->noAuth;
    }
}
