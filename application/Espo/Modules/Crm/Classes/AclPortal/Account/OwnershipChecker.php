<?php


namespace Espo\Modules\Crm\Classes\AclPortal\Account;

use Espo\Entities\User;

use Espo\Modules\Crm\Entities\Account;
use Espo\ORM\Entity;

use Espo\Core\Portal\Acl\OwnershipAccountChecker;


class OwnershipChecker implements OwnershipAccountChecker
{
    public function checkAccount(User $user, Entity $entity): bool
    {
        $accountIdList = $user->getLinkMultipleIdList('accounts');

        if (in_array($entity->getId(), $accountIdList)) {
            return true;
        }

        return false;
    }
}
