<?php


namespace Espo\Core\Api;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Metadata;
use Psr\Http\Server\MiddlewareInterface;


class MiddlewareProvider
{
    public function __construct(
        private Metadata $metadata,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function getGlobalMiddlewareList(): array
    {
        return $this->createFromClassNameList($this->getGlobalMiddlewareClassNameList());
    }

    
    public function getRouteMiddlewareList(Route $route): array
    {
        $key = strtolower($route->getMethod()) . '_' . $route->getRoute();

        
        $classNameList = $this->metadata->get(['app', 'api', 'routeMiddlewareClassNameListMap', $key]) ?? [];

        return $this->createFromClassNameList($classNameList);
    }

    
    public function getActionMiddlewareList(Route $route): array
    {
        $key = strtolower($route->getMethod()) . '_' . $route->getRoute();

        
        $classNameList = $this->metadata->get(['app', 'api', 'actionMiddlewareClassNameListMap', $key]) ?? [];

        return $this->createFromClassNameList($classNameList);
    }

    
    public function getControllerMiddlewareList(string $controller): array
    {
        
        $classNameList = $this->metadata
            ->get(['app', 'api', 'controllerMiddlewareClassNameListMap', $controller]) ?? [];

        return $this->createFromClassNameList($classNameList);
    }

    
    public function getControllerActionMiddlewareList(string $method, string $controller, string $action): array
    {
        $key = $controller . '_' . strtolower($method) . '_' . $action;

        
        $classNameList = $this->metadata
            ->get(['app', 'api', 'controllerActionMiddlewareClassNameListMap', $key]) ?? [];

        return $this->createFromClassNameList($classNameList);
    }

    
    private function getGlobalMiddlewareClassNameList(): array
    {
        return $this->metadata->get(['app', 'api', 'globalMiddlewareClassNameList']) ?? [];
    }

    
    private function createFromClassNameList(array $classNameList): array
    {
        $list = [];

        foreach ($classNameList as $className) {
            $list[] = $this->injectableFactory->create($className);
        }

        return $list;
    }
}
