<?php

declare(strict_types=1);

namespace Slim\Factory\Psr17;

use Slim\Interfaces\ServerRequestCreatorInterface;

class NyholmPsr17Factory extends Psr17Factory
{
    protected static string $responseFactoryClass = 'Nyholm\Psr7\Factory\Psr17Factory';
    protected static string $streamFactoryClass = 'Nyholm\Psr7\Factory\Psr17Factory';
    protected static string $serverRequestCreatorClass = 'Nyholm\Psr7Server\ServerRequestCreator';
    protected static string $serverRequestCreatorMethod = 'fromGlobals';

    
    public static function getServerRequestCreator(): ServerRequestCreatorInterface
    {
        
        $psr17Factory = new static::$responseFactoryClass();

        $serverRequestCreator = new static::$serverRequestCreatorClass(
            $psr17Factory,
            $psr17Factory,
            $psr17Factory,
            $psr17Factory
        );

        return new ServerRequestCreator($serverRequestCreator, static::$serverRequestCreatorMethod);
    }
}
