<?php


namespace Espo\Classes\Acl\Email;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\DefaultAssignmentChecker;

class AssignmentChecker extends DefaultAssignmentChecker
{
    protected function isPermittedAssignedUser(User $user, Entity $entity): bool
    {
        return true;
    }

    protected function isPermittedAssignedUsers(User $user, Entity $entity): bool
    {
        return true;
    }
}
