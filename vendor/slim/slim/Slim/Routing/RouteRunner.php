<?php



declare(strict_types=1);

namespace Slim\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Middleware\RoutingMiddleware;

class RouteRunner implements RequestHandlerInterface
{
    private RouteResolverInterface $routeResolver;

    private RouteParserInterface $routeParser;

    private ?RouteCollectorProxyInterface $routeCollectorProxy;

    public function __construct(
        RouteResolverInterface $routeResolver,
        RouteParserInterface $routeParser,
        ?RouteCollectorProxyInterface $routeCollectorProxy = null
    ) {
        $this->routeResolver = $routeResolver;
        $this->routeParser = $routeParser;
        $this->routeCollectorProxy = $routeCollectorProxy;
    }

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        
        if ($request->getAttribute(RouteContext::ROUTING_RESULTS) === null) {
            $routingMiddleware = new RoutingMiddleware($this->routeResolver, $this->routeParser);
            $request = $routingMiddleware->performRouting($request);
        }

        if ($this->routeCollectorProxy !== null) {
            $request = $request->withAttribute(
                RouteContext::BASE_PATH,
                $this->routeCollectorProxy->getBasePath()
            );
        }

        
        $route = $request->getAttribute(RouteContext::ROUTE);
        return $route->run($request);
    }
}
