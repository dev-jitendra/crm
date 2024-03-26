<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface AccessEntityDeleteChecker extends AccessDeleteChecker
{
    
    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool;
}
