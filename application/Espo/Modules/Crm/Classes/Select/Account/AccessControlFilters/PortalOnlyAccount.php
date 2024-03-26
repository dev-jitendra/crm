<?php


namespace Espo\Modules\Crm\Classes\Select\Account\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder;

use Espo\Entities\User;

class PortalOnlyAccount implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $accountIdList = $this->user->getLinkMultipleIdList(User::LINK_ACCOUNTS);

        if (!count($accountIdList)) {
            $queryBuilder->where(['id' => null]);

            return;
        }

        $queryBuilder->where(['id' => $accountIdList]);
    }
}
