<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;

use Espo\Tools\EntityManager\Rename\Renamer;
use Espo\Tools\EntityManager\Rename\FailReason as RenameFailReason;

class EntityUtil implements Command
{
    private Renamer $renamer;

    public function __construct(Renamer $renamer)
    {
        $this->renamer = $renamer;
    }

    public function run(Params $params, IO $io): void
    {
        $subCommand = $params->getArgument(0);

        if (!$subCommand) {
            $io->writeLine("No sub-command specified.");

            return;
        }

        if ($subCommand === 'rename') {
            $this->runRename($params, $io);

            return;
        }
    }

    private function runRename(Params $params, IO $io): void
    {
        $entityType = $params->getOption('entityType');
        $newName = $params->getOption('newName');

        if (!$entityType) {
            $io->writeLine("No --entity-type option specified.");
        }

        if (!$newName) {
            $io->writeLine("No --new-name option specified.");
        }

        if (!$entityType || !$newName) {
            return;
        }

        $result = $this->renamer->process($entityType, $newName, $io);

        $io->writeLine("");

        if (!$result->isFail()) {
            $io->writeLine("Finished.");

            return;
        }

        $io->setExitStatus(1);
        $io->write("Failed. ");

        $failReason = $result->getFailReason();

        if ($failReason === RenameFailReason::NAME_BAD) {
            $io->writeLine("Name is bad.");

            return;
        }

        if ($failReason === RenameFailReason::NAME_NOT_ALLOWED) {
            $io->writeLine("Name is not allowed.");

            return;
        }

        if ($failReason === RenameFailReason::NAME_TOO_LONG) {
            $io->writeLine("Name is too long.");

            return;
        }

        if ($failReason === RenameFailReason::NAME_TOO_SHORT) {
            $io->writeLine("Name is too short.");

            return;
        }

        if ($failReason === RenameFailReason::NAME_USED) {
            $io->writeLine("Name is already used.");

            return;
        }

        if ($failReason === RenameFailReason::DOES_NOT_EXIST) {
            $io->writeLine("Entity type `{$entityType}` does not exist.");

            return;
        }

        if ($failReason === RenameFailReason::NOT_CUSTOM) {
            $io->writeLine("Entity type `{$entityType}` is not custom, hence can't be renamed.");

            return;
        }

        if ($failReason === RenameFailReason::ENV_NOT_SUPPORTED) {
            $io->writeLine("Environment is not supported.");

            return;
        }

        if ($failReason === RenameFailReason::TABLE_EXISTS) {
            $io->writeLine("Table already exists.");

            return;
        }

        if ($failReason === RenameFailReason::ERROR) {
            $io->writeLine("Error occurred.");

            return;
        }
    }
}
