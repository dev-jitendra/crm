<?php



declare(strict_types=1);

namespace Slim\Psr7\Interfaces;

use InvalidArgumentException;

interface HeadersInterface
{
    
    public function addHeader($name, $value): HeadersInterface;

    
    public function removeHeader(string $name): HeadersInterface;

    
    public function getHeader(string $name, $default = []): array;

    
    public function setHeader($name, $value): HeadersInterface;

    
    public function setHeaders(array $headers): HeadersInterface;

    
    public function hasHeader(string $name): bool;

    
    public function getHeaders(bool $originalCase): array;
}
