<?php


namespace Espo\Core\Api;

use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\StreamInterface;

use Espo\Core\Api\Response as ApiResponse;


class ResponseWrapper implements ApiResponse
{
    public function __construct(private Psr7Response $psr7Response)
    {
        
        $this->psr7Response = $this->psr7Response->withoutHeader('Authorization');
    }

    public function setStatus(int $code, ?string $reason = null): Response
    {
        $this->psr7Response = $this->psr7Response->withStatus($code, $reason ?? '');

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->psr7Response->getStatusCode();
    }

    public function getReasonPhrase(): string
    {
        return $this->psr7Response->getReasonPhrase();
    }

    public function setHeader(string $name, string $value): Response
    {
        $this->psr7Response = $this->psr7Response->withHeader($name, $value);

        return $this;
    }

    public function addHeader(string $name, string $value): Response
    {
        $this->psr7Response = $this->psr7Response->withAddedHeader($name, $value);

        return $this;
    }

    public function getHeader(string $name): ?string
    {
        if (!$this->psr7Response->hasHeader($name)) {
            return null;
        }

        return $this->psr7Response->getHeaderLine($name);
    }

    public function hasHeader(string $name): bool
    {
        return $this->psr7Response->hasHeader($name);
    }

    
    public function getHeaderAsArray(string $name): array
    {
        if (!$this->psr7Response->hasHeader($name)) {
            return [];
        }

        return $this->psr7Response->getHeader($name);
    }

    
    public function getHeaderNames(): array
    {
        return array_keys($this->psr7Response->getHeaders());
    }

    public function writeBody(string $string): Response
    {
        $this->psr7Response->getBody()->write($string);

        return $this;
    }

    public function setBody(StreamInterface $body): Response
    {
        $this->psr7Response = $this->psr7Response->withBody($body);

        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->psr7Response->getBody();
    }

    public function toPsr7(): Psr7Response
    {
        return $this->psr7Response;
    }
}
