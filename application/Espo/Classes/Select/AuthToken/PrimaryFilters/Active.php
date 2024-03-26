<?php


namespace Espo\Classes\Select\AuthToken\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Active implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'isActive' => true,
        ]);
    }
}
