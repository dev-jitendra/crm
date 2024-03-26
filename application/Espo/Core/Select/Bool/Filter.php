<?php


namespace Espo\Core\Select\Bool;

use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;


interface Filter
{
    public function apply(QueryBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void;
}
