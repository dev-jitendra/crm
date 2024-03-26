<?php


namespace Espo\Classes\Acl\Note;

use Espo\Entities\Note;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    
    public function checkOwn(User $user, Entity $entity): bool
    {
        if ($entity->getType() === Note::TYPE_POST && $user->getId() === $entity->getCreatedById()) {
            return true;
        }

        return false;
    }
}
