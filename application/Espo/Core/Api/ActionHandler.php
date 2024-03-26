<?php


namespace Espo\Core\Api;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;

use Espo\Core\Utils\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;


class ActionHandler implements RequestHandlerInterface
{
    private const DEFAULT_CONTENT_TYPE = 'application/json';

    public function __construct(
        private Action $action,
        private ProcessData $processData,
        private Config $config
    ) {}

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestWrapped = new RequestWrapper(
            $request,
            $this->processData->getBasePath(),
            $this->processData->getRouteParams()
        );

        $response = $this->action->process($requestWrapped);

        return $this->prepareResponse($response);
    }

    private function prepareResponse(Response $response): Psr7Response
    {
        if (!$response->hasHeader('Content-Type')) {
            $response->setHeader('Content-Type', self::DEFAULT_CONTENT_TYPE);
        }

        if (!$response->hasHeader('Cache-Control')) {
            $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        }

        if (!$response->hasHeader('Expires')) {
            $response->setHeader('Expires', '0');
        }

        if (!$response->hasHeader('Last-Modified')) {
            $response->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        }

        $response->setHeader('X-App-Timestamp', (string) ($this->config->get('appTimestamp') ?? '0'));

        return $response instanceof ResponseWrapper ?
            $response->toPsr7() :
            self::responseToPsr7($response);
    }

    private static function responseToPsr7(Response $response): Psr7Response
    {
        $psr7Response = (new ResponseFactory())->createResponse();

        $statusCode = $response->getStatusCode();
        $reason = $response->getReasonPhrase();
        $body = $response->getBody();

        $psr7Response = $psr7Response
            ->withStatus($statusCode, $reason)
            ->withBody($body);

        foreach ($response->getHeaderNames() as $name) {
            $psr7Response = $psr7Response->withHeader($name, $response->getHeaderAsArray($name));
        }

        return $psr7Response;
    }
}
