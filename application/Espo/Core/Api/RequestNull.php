<?php


namespace Espo\Core\Api;

use Espo\Core\Api\Request as ApiRequest;

use Psr\Http\Message\UriInterface;

use Slim\Psr7\Factory\UriFactory;

use stdClass;


class RequestNull implements ApiRequest
{
    public function hasQueryParam(string $name): bool
    {
        return false;
    }

    
    public function getQueryParam(string $name): ?string
    {
        return null;
    }

    public function getQueryParams(): array
    {
        return [];
    }

    public function hasRouteParam(string $name): bool
    {
        return false;
    }

    public function getRouteParam(string $name): ?string
    {
        return null;
    }

    public function getRouteParams(): array
    {
        return [];
    }

    public function getHeader(string $name): ?string
    {
        return null;
    }

    public function hasHeader(string $name): bool
    {
        return false;
    }

    
    public function getHeaderAsArray(string $name): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return '';
    }

    public function getUri(): UriInterface
    {
        return (new UriFactory())->createUri();
    }

    public function getResourcePath(): string
    {
        return '';
    }

    public function getBodyContents(): ?string
    {
        return null;
    }

    public function getParsedBody(): stdClass
    {
        return (object) [];
    }

    public function getCookieParam(string $name): ?string
    {
        return null;
    }

    
    public function getServerParam(string $name)
    {
        return null;
    }
}
