<?php


namespace Espo\Tools\Api\Cors;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;

class Middleware implements MiddlewareInterface
{
    private const DEFAULT_SUCCESS_STATUS = 204;
    private const DEFAULT_MAX_AGE = 86400;

    public function __construct(private Helper $helper)
    {}

    public function process(ServerRequest $request, RequestHandler $handler): Response
    {
        $isPreFlight = $request->getMethod() === RequestMethod::METHOD_OPTIONS;

        $response = $isPreFlight ?
            (new ResponseFactory)->createResponse() :
            $handler->handle($request);

        $allowedOrigin = $this->helper->getAllowedOrigin($request);

        if (!$allowedOrigin) {
            return $response;
        }

        $status = $this->helper->getSuccessStatus() ?? self::DEFAULT_SUCCESS_STATUS;
        $allowedMethods = $this->helper->getAllowedMethods($request);
        $allowedHeaders = $this->helper->getAllowedHeaders($request);
        $maxAge = $this->helper->getMaxAge() ?? self::DEFAULT_MAX_AGE;
        $credentialsAllowed = $this->helper->isCredentialsAllowed($request);

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Max-Age', (string) $maxAge);

        if ($credentialsAllowed) {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        if (!$isPreFlight) {
            return $response;
        }

        if ($allowedMethods !== []) {
            $response = $response->withHeader('Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        }

        if ($allowedHeaders !== []) {
            $response = $response->withHeader('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        }

        return $response->withStatus($status);
    }
}
