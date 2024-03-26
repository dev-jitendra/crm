<?php


namespace Espo\Core\Container;

use Espo\Core\Container\Exceptions\NotSettableException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;


interface Container extends ContainerInterface
{
    
    public function get(string $id): object;

    
    public function has(string $id): bool;

    
    public function set(string $id, object $object): void;

    
    public function getClass(string $id): ReflectionClass;

    
    public function getByClass(string $className): object;
}
