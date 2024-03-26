<?php


namespace Espo\Classes\ConsoleCommands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Tools\CategoryTree\RebuildPaths;
use Exception;

class RebuildCategoryPaths implements Command
{
    private RebuildPaths $rebuildPaths;

    public function __construct(RebuildPaths $rebuildPaths)
    {
        $this->rebuildPaths = $rebuildPaths;
    }

    public function run(Params $params, IO $io): void
    {
        $entityType = $params->getArgument(0);

        if (!$entityType) {
            $io->setExitStatus(1);
            $io->writeLine("Error: No entity type. Should be specified as the first argument.");

            return;
        }

        try {
            $this->rebuildPaths->run($entityType);
        }
        catch (Exception $e) {
            $io->setExitStatus(1);
            $io->writeLine("Error: " . $e->getMessage());

            return;
        }

        $io->writeLine("Done.");
    }
}
