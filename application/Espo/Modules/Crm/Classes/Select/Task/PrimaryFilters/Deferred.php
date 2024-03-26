<?php


namespace Espo\Modules\Crm\Classes\Select\Task\PrimaryFilters;

use Espo\Modules\Crm\Entities\Task;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\Core\Select\Primary\Filter;

class Deferred implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where(
            Cond::equal(
                Cond::column('status'),
                Task::STATUS_DEFERRED
            )
        );
    }
}
