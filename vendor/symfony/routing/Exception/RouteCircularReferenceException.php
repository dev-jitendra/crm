<?php



namespace Symfony\Component\Routing\Exception;

class RouteCircularReferenceException extends RuntimeException
{
    public function __construct(string $routeId, array $path)
    {
        parent::__construct(sprintf('Circular reference detected for route "%s", path: "%s".', $routeId, implode(' -> ', $path)));
    }
}
