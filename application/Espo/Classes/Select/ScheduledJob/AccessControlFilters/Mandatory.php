<?php


namespace Espo\Classes\Select\ScheduledJob\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;

use Espo\Core\Select\AccessControl\Filter;

class Mandatory implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'isInternal' => false
        ]);
    }
}
