<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface MiddlewareDispatcherInterface extends RequestHandlerInterface
{
    
    public function add($middleware): self;

    
    public function addMiddleware(MiddlewareInterface $middleware): self;

    
    public function seedMiddlewareStack(RequestHandlerInterface $kernel): void;
}
