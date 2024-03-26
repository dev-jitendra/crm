<?php



declare(strict_types=1);

namespace Slim\Handlers\Strategies;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use RuntimeException;


class RequestResponseNamedArgs implements InvocationStrategyInterface
{
    public function __construct()
    {
        if (PHP_VERSION_ID < 80000) {
            throw new RuntimeException('Named arguments are only available for PHP >= 8.0.0');
        }
    }

    
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        return $callable($request, $response, ...$routeArguments);
    }
}
