<?php


namespace Espo\Modules\Crm\Classes\Select\Task\BoolFilters;

use Espo\Core\Select\Bool\Filter;

use Espo\ORM\Query\SelectBuilder;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\Condition as Cond;

class Completed implements Filter
{
    public function apply(SelectBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $orGroupBuilder->add(
            Cond::equal(
                Cond::column('status'),
                'Completed'
            )
        );
    }
}
