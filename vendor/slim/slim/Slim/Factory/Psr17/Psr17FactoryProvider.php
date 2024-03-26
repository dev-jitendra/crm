<?php



declare(strict_types=1);

namespace Slim\Factory\Psr17;

use Slim\Interfaces\Psr17FactoryProviderInterface;

use function array_unshift;

class Psr17FactoryProvider implements Psr17FactoryProviderInterface
{
    
    protected static array $factories = [
        SlimPsr17Factory::class,
        HttpSoftPsr17Factory::class,
        NyholmPsr17Factory::class,
        LaminasDiactorosPsr17Factory::class,
        GuzzlePsr17Factory::class,
    ];

    
    public static function getFactories(): array
    {
        return static::$factories;
    }

    
    public static function setFactories(array $factories): void
    {
        static::$factories = $factories;
    }

    
    public static function addFactory(string $factory): void
    {
        array_unshift(static::$factories, $factory);
    }
}
