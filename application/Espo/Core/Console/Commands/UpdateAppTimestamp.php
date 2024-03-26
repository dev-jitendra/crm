<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\DataManager;


class UpdateAppTimestamp implements Command
{
    public function __construct(private DataManager $dataManager)
    {}

    public function run(Params $params, IO $io): void
    {
        $this->dataManager->updateAppTimestamp();

        $io->writeLine("App timestamp is updated.");
    }
}
