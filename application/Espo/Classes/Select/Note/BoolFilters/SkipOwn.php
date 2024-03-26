<?php


namespace Espo\Classes\Select\Note\BoolFilters;

use Espo\Core\Select\Bool\Filter;
use Espo\Entities\User;
use Espo\ORM\Query\Part\Condition;
use Espo\ORM\Query\Part\Expression;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class SkipOwn implements Filter
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function apply(QueryBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $orGroupBuilder->add(
            Condition::notEqual(
                Expression::column('createdById'),
                $this->user->getId()
            )
        );
    }
}
