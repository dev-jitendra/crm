<?php



declare(strict_types=1);

namespace Slim\Factory\Psr17;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ServerRequestCreatorInterface;

class ServerRequestCreator implements ServerRequestCreatorInterface
{
    
    protected $serverRequestCreator;

    protected string $serverRequestCreatorMethod;

    
    public function __construct($serverRequestCreator, string $serverRequestCreatorMethod)
    {
        $this->serverRequestCreator = $serverRequestCreator;
        $this->serverRequestCreatorMethod = $serverRequestCreatorMethod;
    }

    
    public function createServerRequestFromGlobals(): ServerRequestInterface
    {
        
        $callable = [$this->serverRequestCreator, $this->serverRequestCreatorMethod];
        return (Closure::fromCallable($callable))();
    }
}
