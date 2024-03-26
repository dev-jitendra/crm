<?php



declare(strict_types=1);

namespace Slim\Psr7;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Interfaces\HeadersInterface;

use function get_class;
use function gettype;
use function is_array;
use function is_null;
use function is_object;
use function is_string;
use function ltrim;
use function parse_str;
use function preg_match;
use function sprintf;
use function str_replace;

class Request extends Message implements ServerRequestInterface
{
    
    protected $method;

    
    protected $uri;

    
    protected $requestTarget;

    
    protected $queryParams;

    protected array $cookies;

    protected array $serverParams;

    protected array $attributes;

    
    protected $parsedBody;

    
    protected array $uploadedFiles;

    
    public function __construct(
        $method,
        UriInterface $uri,
        HeadersInterface $headers,
        array $cookies,
        array $serverParams,
        StreamInterface $body,
        array $uploadedFiles = []
    ) {
        $this->method = $this->filterMethod($method);
        $this->uri = $uri;
        $this->headers = $headers;
        $this->cookies = $cookies;
        $this->serverParams = $serverParams;
        $this->attributes = [];
        $this->body = $body;
        $this->uploadedFiles = $uploadedFiles;

        if (isset($serverParams['SERVER_PROTOCOL'])) {
            $this->protocolVersion = str_replace('HTTP/', '', $serverParams['SERVER_PROTOCOL']);
        }

        if (!$this->headers->hasHeader('Host') || $this->uri->getHost() !== '') {
            $this->headers->setHeader('Host', $this->uri->getHost());
        }
    }

    
    public function __clone()
    {
        $this->headers = clone $this->headers;
        $this->body = clone $this->body;
    }

    
    public function getMethod(): string
    {
        return $this->method;
    }

    
    public function withMethod($method)
    {
        $method = $this->filterMethod($method);
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    
    protected function filterMethod($method): string
    {
        
        if (!is_string($method)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP method; must be a string, received %s',
                (is_object($method) ? get_class($method) : gettype($method))
            ));
        }

        if (preg_match("/^[!#$%&'*+.^_`|~0-9a-z-]+$/i", $method) !== 1) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported HTTP method "%s" provided',
                $method
            ));
        }

        return $method;
    }

    
    public function getRequestTarget(): string
    {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        if ($this->uri === null) {
            return '/';
        }

        $path = $this->uri->getPath();
        $path = '/' . ltrim($path, '/');

        $query = $this->uri->getQuery();
        if ($query) {
            $path .= '?' . $query;
        }

        return $path;
    }

    
    public function withRequestTarget($requestTarget)
    {
        if (!is_string($requestTarget) || preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; must be a string and cannot contain whitespace'
            );
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost && $uri->getHost() !== '') {
            $clone->headers->setHeader('Host', $uri->getHost());
            return $clone;
        }

        if (($uri->getHost() !== '' && !$this->hasHeader('Host') || $this->getHeaderLine('Host') === '')) {
            $clone->headers->setHeader('Host', $uri->getHost());
            return $clone;
        }

        return $clone;
    }

    
    public function getCookieParams(): array
    {
        return $this->cookies;
    }

    
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookies = $cookies;

        return $clone;
    }

    
    public function getQueryParams(): array
    {
        if (is_array($this->queryParams)) {
            return $this->queryParams;
        }

        if ($this->uri === null) {
            return [];
        }

        parse_str($this->uri->getQuery(), $this->queryParams); 
        assert(is_array($this->queryParams));

        return $this->queryParams;
    }

    
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    
    public function withUploadedFiles(array $uploadedFiles)
    {
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    
    public function withoutAttribute($name)
    {
        $clone = clone $this;

        unset($clone->attributes[$name]);

        return $clone;
    }

    
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    
    public function withParsedBody($data)
    {
        
        if (!is_null($data) && !is_object($data) && !is_array($data)) {
            throw new InvalidArgumentException('Parsed body value must be an array, an object, or null');
        }

        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }
}
