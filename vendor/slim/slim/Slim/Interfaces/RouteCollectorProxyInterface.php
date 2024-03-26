<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\UriInterface;

interface RouteCollectorProxyInterface
{
    public function getResponseFactory(): ResponseFactoryInterface;

    public function getCallableResolver(): CallableResolverInterface;

    public function getContainer(): ?ContainerInterface;

    public function getRouteCollector(): RouteCollectorInterface;

    
    public function getBasePath(): string;

    
    public function setBasePath(string $basePath): RouteCollectorProxyInterface;

    
    public function get(string $pattern, $callable): RouteInterface;

    
    public function post(string $pattern, $callable): RouteInterface;

    
    public function put(string $pattern, $callable): RouteInterface;

    
    public function patch(string $pattern, $callable): RouteInterface;

    
    public function delete(string $pattern, $callable): RouteInterface;

    
    public function options(string $pattern, $callable): RouteInterface;

    
    public function any(string $pattern, $callable): RouteInterface;

    
    public function map(array $methods, string $pattern, $callable): RouteInterface;

    
    public function group(string $pattern, $callable): RouteGroupInterface;

    
    public function redirect(string $from, $to, int $status = 302): RouteInterface;
}
