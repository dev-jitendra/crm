<?php


namespace Espo\Classes\Select\Email\BoolFilters;

use Espo\Classes\Select\Email\Helpers\JoinHelper;
use Espo\Core\Select\Bool\Filter;
use Espo\Entities\User;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyMy implements Filter
{
    public function __construct(private User $user, private JoinHelper $joinHelper)
    {}

    public function apply(QueryBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $this->joinHelper->joinEmailUser($queryBuilder, $this->user->getId());

        $item = WhereClause::fromRaw([
            'emailUser.userId' => $this->user->getId(),
        ]);

        $orGroupBuilder->add($item);
    }
}
