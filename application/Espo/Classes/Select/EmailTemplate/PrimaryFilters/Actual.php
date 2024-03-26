<?php


namespace Espo\Classes\Select\EmailTemplate\PrimaryFilters;

use Espo\ORM\Query\SelectBuilder;

use Espo\Core\Select\Primary\Filter;

class Actual implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'oneOff!=' => true
        ]);
    }
}
