<?php



declare(strict_types=1);

namespace Slim\Interfaces;

interface AdvancedCallableResolverInterface extends CallableResolverInterface
{
    
    public function resolveRoute($toResolve): callable;

    
    public function resolveMiddleware($toResolve): callable;
}
