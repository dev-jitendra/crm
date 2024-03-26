<?php


namespace Espo\Classes\Select\EmailFolder\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Select\AccessControl\Filter;
use Espo\Entities\User;

class Mandatory implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $queryBuilder->where([
            'assignedUserId' => $this->user->getId(),
        ]);
    }
}
