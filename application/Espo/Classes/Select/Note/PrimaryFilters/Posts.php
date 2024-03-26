<?php


namespace Espo\Classes\Select\Note\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Entities\Note;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Posts implements Filter
{
    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'type' => Note::TYPE_POST
        ]);
    }
}
