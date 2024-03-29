<?php



namespace Symfony\Component\Routing\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;


class ClosureLoader extends Loader
{
    
    public function load(mixed $closure, string $type = null): RouteCollection
    {
        return $closure($this->env);
    }

    
    public function supports(mixed $resource, string $type = null): bool
    {
        return $resource instanceof \Closure && (!$type || 'closure' === $type);
    }
}
