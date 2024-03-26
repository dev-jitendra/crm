<?php


namespace Espo\Classes\Acl\User;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Acl\OwnershipOwnChecker;
use Espo\Core\Acl\OwnershipTeamChecker;


class OwnershipChecker implements OwnershipOwnChecker, OwnershipTeamChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        return $user->getId() === $entity->getId();
    }

    public function checkTeam(User $user, Entity $entity): bool
    {
        assert($entity instanceof CoreEntity);

        $intersect = array_intersect(
            $user->getLinkMultipleIdList('teams'),
            $entity->getLinkMultipleIdList('teams')
        );

        if (count($intersect)) {
            return true;
        }

        return false;
    }
}
