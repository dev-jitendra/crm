<?php


namespace Espo\Classes\Select\User\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder;

class Internal implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'type!=' => [
                User::TYPE_PORTAL,
                User::TYPE_API,
                User::TYPE_SYSTEM,
            ],
        ]);
    }
}
