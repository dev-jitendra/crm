<?php


namespace Espo\Modules\Crm\Classes\Select\CaseObj\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\CaseObj;
use Espo\ORM\Query\SelectBuilder;

class Closed implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where(['status' => CaseObj::STATUS_CLOSED]);
    }
}
