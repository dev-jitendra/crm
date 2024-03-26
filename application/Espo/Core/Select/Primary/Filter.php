<?php


namespace Espo\Core\Select\Primary;

use Espo\ORM\Query\SelectBuilder as QueryBuilder;


interface Filter
{
    public function apply(QueryBuilder $queryBuilder): void;
}
