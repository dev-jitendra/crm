<?php


namespace Espo\Classes\Select\User\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class ActivePortal implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'isActive' => true,
            'type' => 'portal',
        ]);
    }
}
