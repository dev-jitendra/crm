<?php



declare(strict_types=1);

namespace Slim\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

use function get_class;
use function is_subclass_of;

class ErrorMiddleware implements MiddlewareInterface
{
    protected CallableResolverInterface $callableResolver;

    protected ResponseFactoryInterface $responseFactory;

    protected bool $displayErrorDetails;

    protected bool $logErrors;

    protected bool $logErrorDetails;

    protected ?LoggerInterface $logger = null;

    
    protected array $handlers = [];

    
    protected array $subClassHandlers = [];

    
    protected $defaultErrorHandler;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ) {
        $this->callableResolver = $callableResolver;
        $this->responseFactory = $responseFactory;
        $this->displayErrorDetails = $displayErrorDetails;
        $this->logErrors = $logErrors;
        $this->logErrorDetails = $logErrorDetails;
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->handleException($request, $e);
        }
    }

    public function handleException(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        if ($exception instanceof HttpException) {
            $request = $exception->getRequest();
        }

        $exceptionType = get_class($exception);
        $handler = $this->getErrorHandler($exceptionType);

        return $handler($request, $exception, $this->displayErrorDetails, $this->logErrors, $this->logErrorDetails);
    }

    
    public function getErrorHandler(string $type)
    {
        if (isset($this->handlers[$type])) {
            return $this->callableResolver->resolve($this->handlers[$type]);
        }

        if (isset($this->subClassHandlers[$type])) {
            return $this->callableResolver->resolve($this->subClassHandlers[$type]);
        }

        foreach ($this->subClassHandlers as $class => $handler) {
            if (is_subclass_of($type, $class)) {
                return $this->callableResolver->resolve($handler);
            }
        }

        return $this->getDefaultErrorHandler();
    }

    
    public function getDefaultErrorHandler()
    {
        if ($this->defaultErrorHandler === null) {
            $this->defaultErrorHandler = new ErrorHandler(
                $this->callableResolver,
                $this->responseFactory,
                $this->logger
            );
        }

        return $this->callableResolver->resolve($this->defaultErrorHandler);
    }

    
    public function setDefaultErrorHandler($handler): self
    {
        $this->defaultErrorHandler = $handler;
        return $this;
    }

    
    public function setErrorHandler($typeOrTypes, $handler, bool $handleSubclasses = false): self
    {
        if (is_array($typeOrTypes)) {
            foreach ($typeOrTypes as $type) {
                $this->addErrorHandler($type, $handler, $handleSubclasses);
            }
        } else {
            $this->addErrorHandler($typeOrTypes, $handler, $handleSubclasses);
        }

        return $this;
    }

    
    private function addErrorHandler(string $type, $handler, bool $handleSubclasses): void
    {
        if ($handleSubclasses) {
            $this->subClassHandlers[$type] = $handler;
        } else {
            $this->handlers[$type] = $handler;
        }
    }
}
