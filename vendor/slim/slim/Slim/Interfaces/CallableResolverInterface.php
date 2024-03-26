<?php



declare(strict_types=1);

namespace Slim\Interfaces;

interface CallableResolverInterface
{
    
    public function resolve($toResolve): callable;
}
