<?php


namespace Espo\Modules\Crm\Classes\Select\Lead\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Query\Part\Condition as Cond;

class Actual implements Filter
{
    public function __construct(private Metadata $metadata)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $notActualStatusList = $this->metadata
            ->get(['entityDefs', 'Lead', 'fields', 'status', 'notActualOptions']) ?? [];

        $queryBuilder->where(
            Cond::notIn(
                Cond::column('status'),
                $notActualStatusList
            )
        );
    }
}
