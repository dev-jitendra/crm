<?php


namespace Espo\Modules\Crm\Classes\Select\CaseObj\BoolFilters;

use Espo\Core\Select\Bool\Filter;
use Espo\Core\Utils\Metadata;

use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

class Open implements Filter
{
    public function __construct(private Metadata $metadata)
    {}

    public function apply(SelectBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $notActualStatusList = $this->metadata
            ->get(['entityDefs', 'Case', 'fields', 'status', 'notActualOptions']) ?? [];

        $orGroupBuilder->add(
            Cond::notIn(
                Cond::column('status'),
                $notActualStatusList
            )
        );
    }
}
