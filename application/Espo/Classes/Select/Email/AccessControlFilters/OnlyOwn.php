<?php


namespace Espo\Classes\Select\Email\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;

use Espo\Classes\Select\Email\Helpers\JoinHelper;
use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class OnlyOwn implements Filter
{
    public function __construct(
        private User $user,
        private JoinHelper $joinHelper
    ) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $this->joinHelper->joinEmailUser($queryBuilder, $this->user->getId());

        $queryBuilder->where(['emailUser.userId' => $this->user->getId()]);
    }
}
