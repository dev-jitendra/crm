<?php


namespace Espo\Core\Select\Primary\Filters;

use Espo\Core\Select\Primary\Filter;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;


class One implements Filter
{
    public const NAME = 'one';

    public function apply(QueryBuilder $queryBuilder): void
    {}
}
