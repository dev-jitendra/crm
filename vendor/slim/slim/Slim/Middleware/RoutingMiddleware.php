<?php



declare(strict_types=1);

namespace Slim\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Routing\RouteContext;
use Slim\Routing\RoutingResults;

class RoutingMiddleware implements MiddlewareInterface
{
    protected RouteResolverInterface $routeResolver;

    protected RouteParserInterface $routeParser;

    public function __construct(RouteResolverInterface $routeResolver, RouteParserInterface $routeParser)
    {
        $this->routeResolver = $routeResolver;
        $this->routeParser = $routeParser;
    }

    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $this->performRouting($request);
        return $handler->handle($request);
    }

    
    public function performRouting(ServerRequestInterface $request): ServerRequestInterface
    {
        $request = $request->withAttribute(RouteContext::ROUTE_PARSER, $this->routeParser);

        $routingResults = $this->resolveRoutingResultsFromRequest($request);
        $routeStatus = $routingResults->getRouteStatus();

        $request = $request->withAttribute(RouteContext::ROUTING_RESULTS, $routingResults);

        switch ($routeStatus) {
            case RoutingResults::FOUND:
                $routeArguments = $routingResults->getRouteArguments();
                $routeIdentifier = $routingResults->getRouteIdentifier() ?? '';
                $route = $this->routeResolver
                    ->resolveRoute($routeIdentifier)
                    ->prepare($routeArguments);
                return $request->withAttribute(RouteContext::ROUTE, $route);

            case RoutingResults::NOT_FOUND:
                throw new HttpNotFoundException($request);

            case RoutingResults::METHOD_NOT_ALLOWED:
                $exception = new HttpMethodNotAllowedException($request);
                $exception->setAllowedMethods($routingResults->getAllowedMethods());
                throw $exception;

            default:
                throw new RuntimeException('An unexpected error occurred while performing routing.');
        }
    }

    
    protected function resolveRoutingResultsFromRequest(ServerRequestInterface $request): RoutingResults
    {
        return $this->routeResolver->computeRoutingResults(
            $request->getUri()->getPath(),
            $request->getMethod()
        );
    }
}
