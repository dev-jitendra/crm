<?php


namespace Espo\Core\Portal\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;

use Espo\Core\Acl\OwnershipChecker;


interface OwnershipAccountChecker extends OwnershipChecker
{
    
    public function checkAccount(User $user, Entity $entity): bool;
}
