<?php


namespace Espo\Classes\Select\Team\AccessControlFilters;

use Espo\Entities\User;
use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\SelectBuilder;

class OnlyTeam implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('users', 'usersOnlyMyAccess')
            ->distinct()
            ->where(
                Cond::equal(
                    Cond::column('usersOnlyMyAccess.id'),
                    $this->user->getId()
                )
            );
    }
}
