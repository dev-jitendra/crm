<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Api\ErrorOutput;
use Espo\Core\Api\RequestWrapper;
use Espo\Core\Api\ResponseWrapper;
use Espo\Core\Application\Runner\Params;
use Espo\Core\Application\RunnerParameterized;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Portal\Application as PortalApplication;
use Espo\Core\Portal\ApplicationRunners\Client as PortalPortalClient;
use Espo\Core\Portal\Utils\Url;
use Espo\Core\Utils\ClientManager;
use Espo\Core\Utils\Config;

use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Response;
use Slim\ResponseEmitter;

use Exception;


class PortalClient implements RunnerParameterized
{

    public function __construct(
        private ClientManager $clientManager,
        private Config $config,
        private ErrorOutput $errorOutput
    ) {}

    public function run(Params $params): void
    {
        $id = $params->get('id') ??
            Url::detectPortalId() ??
            $this->config->get('defaultPortalId');

        $basePath = $params->get('basePath') ?? $this->clientManager->getBasePath();

        $requestWrapped = new RequestWrapper(
            ServerRequestCreatorFactory::create()->createServerRequestFromGlobals()
        );

        $responseWrapped = new ResponseWrapper(new Response());

        if ($requestWrapped->getMethod() !== 'GET') {
            throw new BadRequest("Only GET request is allowed.");
        }

        try {
            if (!$id) {
                throw new NotFound("Portal ID not detected.");
            }

            $application = new PortalApplication($id);
        }
        catch (Exception $e) {
            $this->processError($requestWrapped, $responseWrapped, $e);

            return;
        }

        $application->setClientBasePath($basePath);

        $application->run(PortalPortalClient::class);
    }

    private function processError(RequestWrapper $request, ResponseWrapper $response, Exception $exception): void
    {
        $this->errorOutput->processWithBodyPrinting($request, $response, $exception);

        (new ResponseEmitter())->emit($response->toPsr7());
    }
}
