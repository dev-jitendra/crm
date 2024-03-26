<?php


namespace Espo\Classes\Acl\Team;

use Espo\Entities\Team;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;


class OwnershipChecker implements OwnershipOwnChecker
{
    public function checkOwn(User $user, Entity $entity): bool
    {
        
        $userTeamIdList = $user->getLinkMultipleIdList('teams');

        return in_array($entity->getId(), $userTeamIdList);
    }
}
