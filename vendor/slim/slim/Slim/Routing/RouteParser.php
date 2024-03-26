<?php



declare(strict_types=1);

namespace Slim\Routing;

use FastRoute\RouteParser\Std;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;

use function array_key_exists;
use function array_reverse;
use function http_build_query;
use function implode;
use function is_string;

class RouteParser implements RouteParserInterface
{
    private RouteCollectorInterface $routeCollector;

    private Std $routeParser;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
        $this->routeParser = new Std();
    }

    
    public function relativeUrlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        $route = $this->routeCollector->getNamedRoute($routeName);
        $pattern = $route->getPattern();

        $segments = [];
        $segmentName = '';

        
        $expressions = array_reverse($this->routeParser->parse($pattern));
        foreach ($expressions as $expression) {
            foreach ($expression as $segment) {
                
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }

                
                
                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }

                $segments[] = $data[$segment[0]];
            }

            
            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }

        $url = implode('', $segments);
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    
    public function urlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        $basePath = $this->routeCollector->getBasePath();
        $url = $this->relativeUrlFor($routeName, $data, $queryParams);

        if ($basePath) {
            $url = $basePath . $url;
        }

        return $url;
    }

    
    public function fullUrlFor(UriInterface $uri, string $routeName, array $data = [], array $queryParams = []): string
    {
        $path = $this->urlFor($routeName, $data, $queryParams);
        $scheme = $uri->getScheme();
        $authority = $uri->getAuthority();
        $protocol = ($scheme ? $scheme . ':' : '') . ($authority ? '
        return $protocol . $path;
    }
}
