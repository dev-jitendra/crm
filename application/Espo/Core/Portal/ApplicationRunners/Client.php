<?php


namespace Espo\Core\Portal\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\ApplicationState;
use Espo\Core\Utils\ClientManager;


class Client implements Runner
{
    public function __construct(
        private ClientManager $clientManager,
        private ApplicationState $applicationState
    ) {}

    public function run(): void
    {
        $portalId = $this->applicationState->getPortal()->getId();

        $this->clientManager->display(null, null, [
            'portalId' => $portalId,
            'applicationId' => $portalId,
            'apiUrl' => 'api/v1/portal-access/' . $portalId,
            'appClientClassName' => 'app-portal',
        ]);
    }
}
