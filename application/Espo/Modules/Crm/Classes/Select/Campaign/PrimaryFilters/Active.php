<?php


namespace Espo\Modules\Crm\Classes\Select\Campaign\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\Campaign;
use Espo\ORM\Query\SelectBuilder;

class Active implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where(['status' => Campaign::TYPE_ACTIVE]);
    }
}
