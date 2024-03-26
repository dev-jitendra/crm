<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use Psr\Http\Server\MiddlewareInterface;
use Slim\MiddlewareDispatcher;

interface RouteGroupInterface
{
    public function collectRoutes(): RouteGroupInterface;

    
    public function add($middleware): RouteGroupInterface;

    
    public function addMiddleware(MiddlewareInterface $middleware): RouteGroupInterface;

    
    public function appendMiddlewareToDispatcher(MiddlewareDispatcher $dispatcher): RouteGroupInterface;

    
    public function getPattern(): string;
}
