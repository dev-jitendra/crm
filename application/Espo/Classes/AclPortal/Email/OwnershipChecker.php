<?php


namespace Espo\Classes\AclPortal\Email;

use Espo\Entities\Email;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        if ($user->getId() === $entity->get('createdById')) {
            return true;
        }

        return false;
    }
}
