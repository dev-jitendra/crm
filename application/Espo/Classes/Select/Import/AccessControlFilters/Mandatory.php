<?php


namespace Espo\Classes\Select\Import\AccessControlFilters;

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
            'createdById' => $this->user->getId(),
        ]);
    }
}
