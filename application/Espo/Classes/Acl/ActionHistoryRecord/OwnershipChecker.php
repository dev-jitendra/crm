<?php


namespace Espo\Classes\Acl\ActionHistoryRecord;

use Espo\Entities\ActionHistoryRecord;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        return $entity->get('userId') === $user->getId();
    }
}
