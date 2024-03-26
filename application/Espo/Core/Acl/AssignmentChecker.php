<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;

use Espo\Entities\User;


interface AssignmentChecker
{
    
    public function check(User $user, Entity $entity): bool;
}
