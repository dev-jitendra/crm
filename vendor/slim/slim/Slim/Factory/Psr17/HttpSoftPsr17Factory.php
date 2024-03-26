<?php



declare(strict_types=1);

namespace Slim\Factory\Psr17;

class HttpSoftPsr17Factory extends Psr17Factory
{
    protected static string $responseFactoryClass = 'HttpSoft\Message\ResponseFactory';
    protected static string $streamFactoryClass = 'HttpSoft\Message\StreamFactory';
    protected static string $serverRequestCreatorClass = 'HttpSoft\ServerRequest\ServerRequestCreator';
    protected static string $serverRequestCreatorMethod = 'createFromGlobals';
}
