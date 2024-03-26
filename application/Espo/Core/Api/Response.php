<?php


namespace Espo\Core\Api;

use Psr\Http\Message\StreamInterface;


interface Response
{
    
    public function getStatusCode(): int;

    
    public function getReasonPhrase(): string;

    
    public function setStatus(int $code, ?string $reason = null): self;

    
    public function setHeader(string $name, string $value): self;

    
    public function addHeader(string $name, string $value): self;

    
    public function getHeader(string $name): ?string;

    
    public function hasHeader(string $name): bool;

    
    public function getHeaderNames(): array;

    
    public function getHeaderAsArray(string $name): array;

    
    public function writeBody(string $string): self;

    
    public function setBody(StreamInterface $body): self;

    
    public function getBody(): StreamInterface;
}
