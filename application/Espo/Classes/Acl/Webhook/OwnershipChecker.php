<?php


namespace Espo\Classes\Acl\Webhook;

use Espo\Entities\User;
use Espo\ORM\Entity;

use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        return $user->getId() === $entity->get('userId') && $user->isApi();
    }
}
