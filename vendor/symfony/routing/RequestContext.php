<?php



namespace Symfony\Component\Routing;

use Symfony\Component\HttpFoundation\Request;


class RequestContext
{
    private string $baseUrl;
    private string $pathInfo;
    private string $method;
    private string $host;
    private string $scheme;
    private int $httpPort;
    private int $httpsPort;
    private string $queryString;
    private array $parameters = [];

    public function __construct(string $baseUrl = '', string $method = 'GET', string $host = 'localhost', string $scheme = 'http', int $httpPort = 80, int $httpsPort = 443, string $path = '/', string $queryString = '')
    {
        $this->setBaseUrl($baseUrl);
        $this->setMethod($method);
        $this->setHost($host);
        $this->setScheme($scheme);
        $this->setHttpPort($httpPort);
        $this->setHttpsPort($httpsPort);
        $this->setPathInfo($path);
        $this->setQueryString($queryString);
    }

    public static function fromUri(string $uri, string $host = 'localhost', string $scheme = 'http', int $httpPort = 80, int $httpsPort = 443): self
    {
        $uri = parse_url($uri);
        $scheme = $uri['scheme'] ?? $scheme;
        $host = $uri['host'] ?? $host;

        if (isset($uri['port'])) {
            if ('http' === $scheme) {
                $httpPort = $uri['port'];
            } elseif ('https' === $scheme) {
                $httpsPort = $uri['port'];
            }
        }

        return new self($uri['path'] ?? '', 'GET', $host, $scheme, $httpPort, $httpsPort);
    }

    
    public function fromRequest(Request $request): static
    {
        $this->setBaseUrl($request->getBaseUrl());
        $this->setPathInfo($request->getPathInfo());
        $this->setMethod($request->getMethod());
        $this->setHost($request->getHost());
        $this->setScheme($request->getScheme());
        $this->setHttpPort($request->isSecure() || null === $request->getPort() ? $this->httpPort : $request->getPort());
        $this->setHttpsPort($request->isSecure() && null !== $request->getPort() ? $request->getPort() : $this->httpsPort);
        $this->setQueryString($request->server->get('QUERY_STRING', ''));

        return $this;
    }

    
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    
    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = rtrim($baseUrl, '/');

        return $this;
    }

    
    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }

    
    public function setPathInfo(string $pathInfo): static
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }

    
    public function getMethod(): string
    {
        return $this->method;
    }

    
    public function setMethod(string $method): static
    {
        $this->method = strtoupper($method);

        return $this;
    }

    
    public function getHost(): string
    {
        return $this->host;
    }

    
    public function setHost(string $host): static
    {
        $this->host = strtolower($host);

        return $this;
    }

    
    public function getScheme(): string
    {
        return $this->scheme;
    }

    
    public function setScheme(string $scheme): static
    {
        $this->scheme = strtolower($scheme);

        return $this;
    }

    
    public function getHttpPort(): int
    {
        return $this->httpPort;
    }

    
    public function setHttpPort(int $httpPort): static
    {
        $this->httpPort = $httpPort;

        return $this;
    }

    
    public function getHttpsPort(): int
    {
        return $this->httpsPort;
    }

    
    public function setHttpsPort(int $httpsPort): static
    {
        $this->httpsPort = $httpsPort;

        return $this;
    }

    
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    
    public function setQueryString(?string $queryString): static
    {
        
        $this->queryString = (string) $queryString;

        return $this;
    }

    
    public function getParameters(): array
    {
        return $this->parameters;
    }

    
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    
    public function getParameter(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    
    public function hasParameter(string $name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }

    
    public function setParameter(string $name, mixed $parameter): static
    {
        $this->parameters[$name] = $parameter;

        return $this;
    }

    public function isSecure(): bool
    {
        return 'https' === $this->scheme;
    }
}
