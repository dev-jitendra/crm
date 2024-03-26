<?php


namespace Espo\Classes\Select\Team\BoolFilters;

use Espo\Entities\User;

use Espo\Core\Select\Bool\Filter;

use Espo\ORM\Query\{
    SelectBuilder,
    Part\Where\OrGroupBuilder,
    Part\Condition as Cond,
};

class OnlyMy implements Filter
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function apply(SelectBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $queryBuilder
            ->leftJoin('users', 'usersOnlyMyFilter')
            ->distinct();

        $orGroupBuilder->add(
            Cond::equal(
                Cond::column('usersOnlyMyFilter.id'),
                $this->user->getId()
            )
        );
    }
}
