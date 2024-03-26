<?php


namespace Espo\Modules\Crm\Classes\Select\MassEmail\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder;

use Espo\Entities\User;

class OnlyOwn implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('campaign', 'campaignAccess')
            ->where([
                'campaignAccess.assignedUserId' => $this->user->getId(),
            ]);
    }
}
