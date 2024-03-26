<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Console\CommandManager as ConsoleCommandManager;

use Exception;


class Command implements Runner
{
    use Cli;
    use SetupSystemUser;

    public function __construct(private ConsoleCommandManager $commandManager)
    {}

    public function run(): void
    {
        try {
            $exitStatus = $this->commandManager->run($_SERVER['argv']);
        }
        catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";

            exit(1);
        }

        exit($exitStatus);
    }
}
