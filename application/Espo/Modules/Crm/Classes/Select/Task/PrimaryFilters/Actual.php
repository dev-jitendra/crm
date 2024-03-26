<?php


namespace Espo\Modules\Crm\Classes\Select\Task\PrimaryFilters;

use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

use Espo\Core\Select\Primary\Filter;
use Espo\Core\Utils\Metadata;

class Actual implements Filter
{
    public function __construct(private Metadata $metadata)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $notActualStatusList = $this->metadata
            ->get(['entityDefs', 'Task', 'fields', 'status', 'notActualOptions']) ?? [];

        $queryBuilder->where(
            Cond::notIn(
                Cond::column('status'),
                $notActualStatusList
            )
        );
    }
}
