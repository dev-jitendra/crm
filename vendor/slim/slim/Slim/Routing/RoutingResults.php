<?php



declare(strict_types=1);

namespace Slim\Routing;

use Slim\Interfaces\DispatcherInterface;

use function rawurldecode;

class RoutingResults
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    protected DispatcherInterface $dispatcher;

    protected string $method;

    protected string $uri;

    
    protected int $routeStatus;

    protected ?string $routeIdentifier = null;

    
    protected array $routeArguments;

    
    public function __construct(
        DispatcherInterface $dispatcher,
        string $method,
        string $uri,
        int $routeStatus,
        ?string $routeIdentifier = null,
        array $routeArguments = []
    ) {
        $this->dispatcher = $dispatcher;
        $this->method = $method;
        $this->uri = $uri;
        $this->routeStatus = $routeStatus;
        $this->routeIdentifier = $routeIdentifier;
        $this->routeArguments = $routeArguments;
    }

    public function getDispatcher(): DispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getRouteStatus(): int
    {
        return $this->routeStatus;
    }

    public function getRouteIdentifier(): ?string
    {
        return $this->routeIdentifier;
    }

    
    public function getRouteArguments(bool $urlDecode = true): array
    {
        if (!$urlDecode) {
            return $this->routeArguments;
        }

        $routeArguments = [];
        foreach ($this->routeArguments as $key => $value) {
            $routeArguments[$key] = rawurldecode($value);
        }

        return $routeArguments;
    }

    
    public function getAllowedMethods(): array
    {
        return $this->dispatcher->getAllowedMethods($this->uri);
    }
}
