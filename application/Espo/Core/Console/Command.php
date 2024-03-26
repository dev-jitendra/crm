<?php


namespace Espo\Core\Console;

use Espo\Core\Console\Command\Params;


interface Command
{
    public function run(Params $params, IO $io): void;
}
