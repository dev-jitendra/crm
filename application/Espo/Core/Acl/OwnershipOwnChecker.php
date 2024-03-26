<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface OwnershipOwnChecker extends OwnershipChecker
{
    
    public function checkOwn(User $user, Entity $entity): bool;
}
