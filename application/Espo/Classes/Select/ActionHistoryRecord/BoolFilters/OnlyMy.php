<?php


namespace Espo\Classes\Select\ActionHistoryRecord\BoolFilters;

use Espo\Core\Select\Bool\Filter;
use Espo\Entities\User;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyMy implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(QueryBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $item = WhereClause::fromRaw([
            'userId' => $this->user->getId(),
        ]);

        $orGroupBuilder->add($item);
    }
}
