<?php


namespace Espo\Core\Portal\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;

use Espo\Core\Acl\OwnershipChecker;


interface OwnershipContactChecker extends OwnershipChecker
{
    
    public function checkContact(User $user, Entity $entity): bool;
}
