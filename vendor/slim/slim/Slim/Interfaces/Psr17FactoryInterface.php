<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;

interface Psr17FactoryInterface
{
    
    public static function getResponseFactory(): ResponseFactoryInterface;

    
    public static function getStreamFactory(): StreamFactoryInterface;

    
    public static function getServerRequestCreator(): ServerRequestCreatorInterface;

    
    public static function isResponseFactoryAvailable(): bool;

    
    public static function isStreamFactoryAvailable(): bool;

    
    public static function isServerRequestCreatorAvailable(): bool;
}
