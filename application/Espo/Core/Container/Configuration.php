<?php


namespace Espo\Core\Container;

interface Configuration
{
    
    public function getLoaderClassName(string $name): ?string;

    
    public function getServiceClassName(string $name): ?string;

    
    public function getServiceDependencyList(string $name): ?array;

    public function isSettable(string $name): bool;
}
