<?php


namespace Espo\Core\Select\Where;

use Espo\ORM\Query\Part\WhereItem as WhereClauseItem;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;


interface ItemConverter
{
    public function convert(QueryBuilder $queryBuilder, Item $item): WhereClauseItem;
}
