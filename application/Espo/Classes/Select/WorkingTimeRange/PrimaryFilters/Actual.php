<?php


namespace Espo\Classes\Select\WorkingTimeRange\PrimaryFilters;

use Espo\Core\Field\Date;
use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Actual implements Filter
{
    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->where(
            Expression::greaterOrEqual(
                Expression::column('dateEnd'),
                Date::createToday()->toString()
            )
        );
    }
}
