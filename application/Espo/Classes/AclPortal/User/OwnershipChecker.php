<?php


namespace Espo\Classes\AclPortal\User;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        return $user->getId() === $entity->getId();
    }
}
