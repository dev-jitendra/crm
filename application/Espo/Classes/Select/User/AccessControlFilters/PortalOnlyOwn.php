<?php


namespace Espo\Classes\Select\User\AccessControlFilters;

use Espo\ORM\Query\SelectBuilder;

use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;

class PortalOnlyOwn implements Filter
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'id' => $this->user->getId(),
        ]);
    }
}
