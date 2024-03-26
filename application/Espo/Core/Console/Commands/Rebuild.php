<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\DataManager;
use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Database\Schema\RebuildMode;

class Rebuild implements Command
{
    public function __construct(private DataManager $dataManager)
    {}

    
    public function run(Params $params, IO $io): void
    {
        $this->dataManager->rebuild();

        if ($params->hasFlag('hard')) {
            $message =
                "Are you sure you want to run a hard DB rebuild? It will drop unused columns, " .
                "decrease exceeding column lengths. It may take some time to process.\nType [Y] to proceed.";

            $io->writeLine($message);

            $input = $io->readLine();

            if (strtolower($input) !== 'y') {
                return;
            }

            $this->dataManager->rebuildDatabase(null, RebuildMode::HARD);
        }

        $io->writeLine("Rebuild has been done.");
    }
}
