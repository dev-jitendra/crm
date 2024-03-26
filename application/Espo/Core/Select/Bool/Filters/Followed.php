<?php


namespace Espo\Core\Select\Bool\Filters;

use Espo\Core\Select\Bool\Filter;
use Espo\Entities\Subscription;
use Espo\Entities\User;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereClause;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Followed implements Filter
{
    public function __construct(private string $entityType, private User $user)
    {}

    public function apply(QueryBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $alias = 'subscriptionFollowedBoolFilter';

        $queryBuilder->leftJoin(
            Subscription::ENTITY_TYPE,
            $alias,
            [
                $alias . '.entityType' => $this->entityType,
                $alias . '.entityId=:' => 'id',
                $alias . '.userId' => $this->user->getId(),
            ]
        );

        $orGroupBuilder->add(
            WhereClause::fromRaw([
                $alias . '.id!=' => null,
            ])
        );
    }
}
