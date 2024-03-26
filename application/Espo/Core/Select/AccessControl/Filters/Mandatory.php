<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Mandatory implements Filter
{
    public function apply(QueryBuilder $queryBuilder): void
    {}
}
