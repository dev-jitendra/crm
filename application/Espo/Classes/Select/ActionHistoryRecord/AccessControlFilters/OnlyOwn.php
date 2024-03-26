<?php


namespace Espo\Classes\Select\ActionHistoryRecord\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyOwn implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'userId' => $this->user->getId(),
        ]);
    }
}
