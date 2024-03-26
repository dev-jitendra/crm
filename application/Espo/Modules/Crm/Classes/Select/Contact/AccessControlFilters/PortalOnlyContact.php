<?php


namespace Espo\Modules\Crm\Classes\Select\Contact\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\ORM\Query\SelectBuilder;
use Espo\Entities\User;

class PortalOnlyContact implements Filter
{

    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        $contactId = $this->user->getContactId();

        if ($contactId === null) {
            $queryBuilder->where(['id' => null]);

            return;
        }

        $queryBuilder->where(['id' => $contactId]);
    }
}
