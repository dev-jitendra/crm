<?php


namespace Espo\Core\Select\Applier\Appliers;

use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Limit
{
    public function apply(QueryBuilder $queryBuilder, ?int $offset, ?int $maxSize): void
    {
        $queryBuilder->limit($offset, $maxSize);
    }
}
