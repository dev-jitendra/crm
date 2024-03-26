<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\DataManager;
use Espo\Core\Utils\Log;
use Exception;


class Rebuild implements Runner
{
    use Cli;

    public function __construct(private DataManager $dataManager, private Log $log)
    {}

    public function run(): void
    {
        try {
            $this->dataManager->rebuild();
        }
        catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";

            $this->log->error('Rebuild: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            exit(1);
        }
    }
}
