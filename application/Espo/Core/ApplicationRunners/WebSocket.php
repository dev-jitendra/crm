<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\WebSocket\ServerStarter;


class WebSocket implements Runner
{
    use Cli;

    public function __construct(private ServerStarter $serverStarter)
    {}

    public function run(): void
    {
        $this->serverStarter->start();
    }
}
