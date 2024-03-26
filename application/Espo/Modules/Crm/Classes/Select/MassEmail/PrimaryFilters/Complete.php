<?php


namespace Espo\Modules\Crm\Classes\Select\MassEmail\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\MassEmail;
use Espo\ORM\Query\SelectBuilder;

class Complete implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'status' => MassEmail::STATUS_COMPLETE,
        ]);
    }
}
