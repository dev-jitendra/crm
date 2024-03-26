<?php



declare(strict_types=1);

namespace Slim\Factory\Psr17;

class GuzzlePsr17Factory extends Psr17Factory
{
    protected static string $responseFactoryClass = 'GuzzleHttp\Psr7\HttpFactory';
    protected static string $streamFactoryClass = 'GuzzleHttp\Psr7\HttpFactory';
    protected static string $serverRequestCreatorClass = 'GuzzleHttp\Psr7\ServerRequest';
    protected static string $serverRequestCreatorMethod = 'fromGlobals';
}
