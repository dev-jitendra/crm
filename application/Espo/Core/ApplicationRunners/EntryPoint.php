<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\RunnerParameterized;
use Espo\Core\Application\Runner\Params;
use Espo\Core\EntryPoint\Starter;


class EntryPoint implements RunnerParameterized
{
    public function __construct(private Starter $starter)
    {}

    public function run(Params $params): void
    {
        $this->starter->start(
            $params->get('entryPoint'),
            $params->get('final') ?? false
        );
    }
}
