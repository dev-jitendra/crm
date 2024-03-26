<?php


namespace Espo\Modules\Crm\Classes\Select\Account\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;

class Partners implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'type' => 'Partner',
        ]);
    }
}
