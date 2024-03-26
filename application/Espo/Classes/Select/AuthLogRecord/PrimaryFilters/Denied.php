<?php


namespace Espo\Classes\Select\AuthLogRecord\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Denied implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'isDenied' => true,
        ]);
    }
}
