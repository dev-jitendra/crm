<?php


namespace Espo\Core\Application;

use Espo\Core\Application\Runner\Params;


interface RunnerParameterized
{
    public function run(Params $params): void;
}
