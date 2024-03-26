<?php



declare(strict_types=1);

namespace Slim\Interfaces;

interface Psr17FactoryProviderInterface
{
    
    public static function getFactories(): array;

    
    public static function setFactories(array $factories): void;

    public static function addFactory(string $factory): void;
}
