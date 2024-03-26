<?php


namespace Espo\Core\Api;

use Espo\Core\Utils\Json;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request as ApiRequest;

use Psr\Http\Message\ServerRequestInterface as Psr7Request;
use Psr\Http\Message\UriInterface;

use stdClass;


class RequestWrapper implements ApiRequest
{
    private ?stdClass $parsedBody = null;

    
    public function __construct(
        private Psr7Request $psr7Request,
        private string $basePath = '',
        private array $routeParams = []
    ) {}

    
    public function get(?string $name = null)
    {
        if (is_null($name)) {
            return array_merge(
                $this->getQueryParams(),
                $this->routeParams
            );
        }

        if ($this->hasRouteParam($name)) {
            return $this->getRouteParam($name);
        }

        return $this->psr7Request->getQueryParams()[$name] ?? null;
    }

    public function hasRouteParam(string $name): bool
    {
        return array_key_exists($name, $this->routeParams);
    }

    public function getRouteParam(string $name): ?string
    {
        return $this->routeParams[$name] ?? null;
    }

    
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function hasQueryParam(string $name): bool
    {
        return array_key_exists($name, $this->psr7Request->getQueryParams());
    }

    public function getQueryParam(string $name): ?string
    {
        $value = $this->psr7Request->getQueryParams()[$name] ?? null;

        if (!is_string($value)) {
            return null;
        }

        return $value;
    }

    public function getQueryParams(): array
    {
        return $this->psr7Request->getQueryParams();
    }

    public function getHeader(string $name): ?string
    {
        if (!$this->psr7Request->hasHeader($name)) {
            return null;
        }

        return $this->psr7Request->getHeaderLine($name);
    }

    public function hasHeader(string $name): bool
    {
        return $this->psr7Request->hasHeader($name);
    }

    
    public function getHeaderAsArray(string $name): array
    {
        if (!$this->psr7Request->hasHeader($name)) {
            return [];
        }

        return $this->psr7Request->getHeader($name);
    }

    public function getMethod(): string
    {
        return $this->psr7Request->getMethod();
    }

    public function getContentType(): ?string
    {
        if (!$this->hasHeader('Content-Type')) {
            return null;
        }

        $contentType = explode(
            ';',
            $this->psr7Request->getHeader('Content-Type')[0]
        )[0];

        return strtolower($contentType);
    }

    public function getBodyContents(): ?string
    {
        $contents = $this->psr7Request->getBody()->getContents();

        $this->psr7Request->getBody()->rewind();

        return $contents;
    }

    
    public function getParsedBody(): stdClass
    {
        if ($this->parsedBody === null) {
            $this->initParsedBody();
        }

        if ($this->parsedBody === null) {
            throw new BadRequest();
        }

        return Util::cloneObject($this->parsedBody);
    }

    
    private function initParsedBody(): void
    {
        $contents = $this->getBodyContents();

        $contentType = $this->getContentType();

        if ($contentType === 'application/json' && $contents) {
            $parsedBody = Json::decode($contents);

            if (is_array($parsedBody)) {
                $parsedBody = (object) [
                    'list' => $parsedBody,
                ];
            }

            if (!$parsedBody instanceof stdClass) {
                throw new BadRequest("Body is not a JSON object.");
            }

            $this->parsedBody = $parsedBody;

            return;
        }

        if (
            in_array($contentType, ['application/x-www-form-urlencoded', 'multipart/form-data']) &&
            $contents
        ) {
            $parsedBody = $this->psr7Request->getParsedBody();

            if (is_array($parsedBody)) {
                $this->parsedBody = (object) $parsedBody;

                return;
            }

            if ($parsedBody instanceof stdClass) {
                $this->parsedBody = $parsedBody;

                return;
            }
        }

        $this->parsedBody = (object) [];
    }

    public function getCookieParam(string $name): ?string
    {
        $params = $this->psr7Request->getCookieParams();

        return $params[$name] ?? null;
    }

    
    public function getServerParam(string $name)
    {
        $params = $this->psr7Request->getServerParams();

        return $params[$name] ?? null;
    }

    public function getUri(): UriInterface
    {
        return $this->psr7Request->getUri();
    }

    public function getResourcePath(): string
    {
        $path = $this->psr7Request->getUri()->getPath();

        return substr($path, strlen($this->basePath));
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isPut(): bool
    {
        return $this->getMethod() === 'PUT';
    }

    public function isUpdate(): bool
    {
        return $this->getMethod() === 'UPDATE';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isPatch(): bool
    {
        return $this->getMethod() === 'PATCH';
    }

    public function isDelete(): bool
    {
        return $this->getMethod() === 'DELETE';
    }
}
