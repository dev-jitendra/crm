<?php



declare(strict_types=1);

namespace Slim\Interfaces;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use RuntimeException;

interface RouteParserInterface
{
    
    public function relativeUrlFor(string $routeName, array $data = [], array $queryParams = []): string;

    
    public function urlFor(string $routeName, array $data = [], array $queryParams = []): string;

    
    public function fullUrlFor(UriInterface $uri, string $routeName, array $data = [], array $queryParams = []): string;
}
