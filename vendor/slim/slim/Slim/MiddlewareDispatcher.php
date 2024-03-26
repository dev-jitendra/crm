<?php



declare(strict_types=1);

namespace Slim;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Slim\Interfaces\AdvancedCallableResolverInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;

use function class_exists;
use function function_exists;
use function is_callable;
use function is_string;
use function preg_match;
use function sprintf;

class MiddlewareDispatcher implements MiddlewareDispatcherInterface
{
    
    protected RequestHandlerInterface $tip;

    protected ?CallableResolverInterface $callableResolver;

    protected ?ContainerInterface $container;

    public function __construct(
        RequestHandlerInterface $kernel,
        ?CallableResolverInterface $callableResolver = null,
        ?ContainerInterface $container = null
    ) {
        $this->seedMiddlewareStack($kernel);
        $this->callableResolver = $callableResolver;
        $this->container = $container;
    }

    
    public function seedMiddlewareStack(RequestHandlerInterface $kernel): void
    {
        $this->tip = $kernel;
    }

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->tip->handle($request);
    }

    
    public function add($middleware): MiddlewareDispatcherInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $this->addMiddleware($middleware);
        }

        if (is_string($middleware)) {
            return $this->addDeferred($middleware);
        }

        if (is_callable($middleware)) {
            return $this->addCallable($middleware);
        }

        
        throw new RuntimeException(
            'A middleware must be an object/class name referencing an implementation of ' .
            'MiddlewareInterface or a callable with a matching signature.'
        );
    }

    
    public function addMiddleware(MiddlewareInterface $middleware): MiddlewareDispatcherInterface
    {
        $next = $this->tip;
        $this->tip = new class ($middleware, $next) implements RequestHandlerInterface {
            private MiddlewareInterface $middleware;

            private RequestHandlerInterface $next;

            public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $next)
            {
                $this->middleware = $middleware;
                $this->next = $next;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->next);
            }
        };

        return $this;
    }

    
    public function addDeferred(string $middleware): self
    {
        $next = $this->tip;
        $this->tip = new class (
            $middleware,
            $next,
            $this->container,
            $this->callableResolver
        ) implements RequestHandlerInterface {
            private string $middleware;

            private RequestHandlerInterface $next;

            private ?ContainerInterface $container;

            private ?CallableResolverInterface $callableResolver;

            public function __construct(
                string $middleware,
                RequestHandlerInterface $next,
                ?ContainerInterface $container = null,
                ?CallableResolverInterface $callableResolver = null
            ) {
                $this->middleware = $middleware;
                $this->next = $next;
                $this->container = $container;
                $this->callableResolver = $callableResolver;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if ($this->callableResolver instanceof AdvancedCallableResolverInterface) {
                    $callable = $this->callableResolver->resolveMiddleware($this->middleware);
                    return $callable($request, $this->next);
                }

                $callable = null;

                if ($this->callableResolver instanceof CallableResolverInterface) {
                    try {
                        $callable = $this->callableResolver->resolve($this->middleware);
                    } catch (RuntimeException $e) {
                        
                    }
                }

                if (!$callable) {
                    $resolved = $this->middleware;
                    $instance = null;
                    $method = null;

                    
                    if (preg_match(CallableResolver::$callablePattern, $resolved, $matches)) {
                        $resolved = $matches[1];
                        $method = $matches[2];
                    }

                    if ($this->container && $this->container->has($resolved)) {
                        $instance = $this->container->get($resolved);
                        if ($instance instanceof MiddlewareInterface) {
                            return $instance->process($request, $this->next);
                        }
                    } elseif (!function_exists($resolved)) {
                        if (!class_exists($resolved)) {
                            throw new RuntimeException(sprintf('Middleware %s does not exist', $resolved));
                        }
                        $instance = new $resolved($this->container);
                    }

                    if ($instance && $instance instanceof MiddlewareInterface) {
                        return $instance->process($request, $this->next);
                    }

                    $callable = $instance ?? $resolved;
                    if ($instance && $method) {
                        $callable = [$instance, $method];
                    }

                    if ($this->container && $callable instanceof Closure) {
                        $callable = $callable->bindTo($this->container);
                    }
                }

                if (!is_callable($callable)) {
                    throw new RuntimeException(
                        sprintf(
                            'Middleware %s is not resolvable',
                            $this->middleware
                        )
                    );
                }

                return $callable($request, $this->next);
            }
        };

        return $this;
    }

    
    public function addCallable(callable $middleware): self
    {
        $next = $this->tip;

        if ($this->container && $middleware instanceof Closure) {
            
            $middleware = $middleware->bindTo($this->container);
        }

        $this->tip = new class ($middleware, $next) implements RequestHandlerInterface {
            
            private $middleware;

            
            private $next;

            public function __construct(callable $middleware, RequestHandlerInterface $next)
            {
                $this->middleware = $middleware;
                $this->next = $next;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return ($this->middleware)($request, $this->next);
            }
        };

        return $this;
    }
}
