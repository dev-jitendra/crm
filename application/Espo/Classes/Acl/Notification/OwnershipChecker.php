<?php


namespace Espo\Classes\Acl\Notification;

use Espo\Entities\Notification;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        if ($user->getId() === $entity->get('userId')) {
            return true;
        }

        return false;
    }
}
