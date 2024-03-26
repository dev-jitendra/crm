<?php


namespace Espo\Modules\Crm\Classes\Select\Opportunity\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

use Espo\Modules\Crm\Classes\Select\Opportunity\Utils\StageListPoriver;

class Lost implements Filter
{
    private $stageListPoriver;

    public function __construct(StageListPoriver $stageListPoriver)
    {
        $this->stageListPoriver = $stageListPoriver;
    }

    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where(
            Cond::in(
                Cond::column('stage'),
                array_merge(
                    $this->stageListPoriver->getLost(),
                )
            )
        );
    }
}
