<?php


namespace Espo\Core\Select\Primary\Filters;

use Espo\Core\Select\Primary\Filter;
use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class Followed implements Filter
{
    public function __construct(private string $entityType, private User $user)
    {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $alias = 'subscriptionFollowedPrimaryFilter';

        $queryBuilder->join(
            'Subscription',
            $alias,
            [
                $alias . '.entityType' => $this->entityType,
                $alias . '.entityId=:' => 'id',
                $alias . '.userId' => $this->user->getId(),
            ]
        );
    }
}
