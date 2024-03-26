<?php


namespace Espo\Classes\Acl\Attachment;

use Espo\Entities\Attachment;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    private const ATTR_CREATED_BY_ID = 'createdById';

    public function checkOwn(User $user, Entity $entity): bool
    {
        if ($user->getId() === $entity->get(self::ATTR_CREATED_BY_ID)) {
            return true;
        }

        return false;
    }
}
