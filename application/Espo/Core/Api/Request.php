<?php


namespace Espo\Core\Api;

use Psr\Http\Message\UriInterface;

use stdClass;


interface Request
{
    
    public function hasQueryParam(string $name): bool;

    
    public function getQueryParam(string $name): ?string;

    
    public function getQueryParams(): array;

    
    public function hasRouteParam(string $name): bool;

    
    public function getRouteParam(string $name): ?string;

    
    public function getRouteParams(): array;

    
    public function getHeader(string $name): ?string;

    
    public function hasHeader(string $name): bool;

    
    public function getHeaderAsArray(string $name): array;

    
    public function getMethod(): string;

    
    public function getUri(): UriInterface;

    
    public function getResourcePath(): string;

    
    public function getBodyContents(): ?string;

    
    public function getParsedBody(): stdClass;

    
    public function getCookieParam(string $name): ?string;

    
    public function getServerParam(string $name);
}
