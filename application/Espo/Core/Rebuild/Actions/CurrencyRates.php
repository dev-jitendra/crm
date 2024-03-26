<?php


namespace Espo\Core\Rebuild\Actions;

use Espo\Core\Rebuild\RebuildAction;
use Espo\Core\Utils\Currency\DatabasePopulator;

class CurrencyRates implements RebuildAction
{
    public function __construct(private DatabasePopulator $databasePopulator) {}

    public function process(): void
    {
        $this->databasePopulator->process();
    }
}
