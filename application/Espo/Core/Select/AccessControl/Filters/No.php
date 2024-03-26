<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class No implements Filter
{
    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->where(['id' => null]);
    }
}
