<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use Slim\Routing\RoutingResults;

interface DispatcherInterface
{
    
    public function dispatch(string $method, string $uri): RoutingResults;

    
    public function getAllowedMethods(string $uri): array;
}
