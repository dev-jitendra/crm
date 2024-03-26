<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Utils\ClientManager;
use Espo\Core\Utils\Config;


class Client implements Runner
{
    private ClientManager $clientManager;
    private Config $config;

    public function __construct(ClientManager $clientManager, Config $config)
    {
        $this->clientManager = $clientManager;
        $this->config = $config;
    }

    public function run(): void
    {
        if (!$this->config->get('isInstalled')) {
            header("Location: install/");

            return;
        }

        $this->clientManager->display();
    }
}
