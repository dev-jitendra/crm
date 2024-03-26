<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\Utils\Config;

class Version implements Command
{
    public function __construct(private Config $config)
    {}

    public function run(Params $params, IO $io): void
    {
        $version = $this->config->get('version');

        if (is_null($version)) {
            return;
        }

        $io->writeLine($version);
    }
}
