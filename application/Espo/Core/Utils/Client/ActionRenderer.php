<?php


namespace Espo\Core\Utils\Client;

use Espo\Core\Api\Response;
use Espo\Core\Utils\Client\ActionRenderer\Params;
use Espo\Core\Utils\Json;
use Espo\Core\Utils\ClientManager;


class ActionRenderer
{

    public function __construct(private ClientManager $clientManager)
    {}

    
    public function write(Response $response, Params $params): void
    {
        $body = $this->render(
            $params->getController(),
            $params->getAction(),
            $params->getData(),
            $params->initAuth()
        );

        $this->clientManager->writeHeaders($response);
        $response->writeBody($body);
    }

    
    public function render(string $controller, string $action, ?array $data = null, bool $initAuth = false): string
    {
        $encodedData = Json::encode($data);

        $initAuthPart = $initAuth ? "app.initAuth();" : '';

        $script =
            "
                {$initAuthPart}
                app.doAction({
                    controllerClassName: '{$controller}',
                    action: '{$action}',
                    options: {$encodedData},
                });
            ";

        return $this->clientManager->render($script);
    }
}
