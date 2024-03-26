<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;

use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Util;

class Help implements Command
{
    private $metadata;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function run(Params $params, IO $io): void
    {
        
        $fullCommandList = array_keys($this->metadata->get(['app', 'consoleCommands']) ?? []);

        $commandList = array_filter(
            $fullCommandList,
            function ($item): bool {
                return (bool) $this->metadata->get(['app', 'consoleCommands', $item, 'listed']);
            }
        );

        sort($commandList);

        $io->writeLine("");
        $io->writeLine("Available commands:");
        $io->writeLine("");

        foreach ($commandList as $item) {
            $io->writeLine(
                ' ' . Util::camelCaseToHyphen($item)
            );
        }

        $io->writeLine("");

        $io->writeLine("Usage:");
        $io->writeLine("");
        $io->writeLine(" bin/command [command-name] [some-argument] [--some-option=value] [--some-flag]");

        $io->writeLine("");

        $io->writeLine("Documentation: https:

        $io->writeLine("");
    }
}
