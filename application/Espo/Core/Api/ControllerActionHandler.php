<?php


namespace Espo\Core\Api;

use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;


class ControllerActionHandler implements RequestHandlerInterface
{
    public function __construct(
        private string $controllerName,
        private string $actionName,
        private ProcessData $processData,
        private ResponseWrapper $responseWrapped,
        private ControllerActionProcessor $controllerActionProcessor,
        private Config $config
    ) {}

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $requestWrapped = new RequestWrapper(
            $request,
            $this->processData->getBasePath(),
            $this->processData->getRouteParams()
        );

        $this->beforeProceed();

        $responseWrapped = $this->controllerActionProcessor->process(
            $this->controllerName,
            $this->actionName,
            $requestWrapped,
            $this->responseWrapped
        );

        $this->afterProceed($responseWrapped);

        return $responseWrapped->toPsr7();
    }

    private function beforeProceed(): void
    {
        $this->responseWrapped->setHeader('Content-Type', 'application/json');
    }

    private function afterProceed(Response $responseWrapped): void
    {
        $responseWrapped
            ->setHeader('X-App-Timestamp', (string) ($this->config->get('appTimestamp') ?? '0'))
            ->setHeader('Expires', '0')
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->setHeader('Pragma', 'no-cache');
    }
}
