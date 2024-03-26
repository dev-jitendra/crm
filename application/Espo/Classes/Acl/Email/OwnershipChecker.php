<?php


namespace Espo\Classes\Acl\Email;

use Espo\Entities\User;
use Espo\Entities\Email;

use Espo\ORM\Entity;

use Espo\Core\Acl\DefaultOwnershipChecker;
use Espo\Core\Acl\OwnershipOwnChecker;
use Espo\Core\Acl\OwnershipTeamChecker;


class OwnershipChecker implements OwnershipOwnChecker, OwnershipTeamChecker
{
    private $defaultOwnershipChecker;

    public function __construct(DefaultOwnershipChecker $defaultOwnershipChecker)
    {
        $this->defaultOwnershipChecker = $defaultOwnershipChecker;
    }

    public function checkOwn(User $user, Entity $entity): bool
    {
        

        if ($user->getId() === $entity->get('assignedUserId')) {
            return true;
        }

        if ($user->getId() === $entity->get('createdById')) {
            return true;
        }

        if ($entity->hasLinkMultipleId('assignedUsers', $user->getId())) {
            return true;
        }

        return false;
    }

    public function checkTeam(User $user, Entity $entity): bool
    {
        return $this->defaultOwnershipChecker->checkTeam($user, $entity);
    }
}
