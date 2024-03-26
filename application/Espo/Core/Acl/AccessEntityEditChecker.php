<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface AccessEntityEditChecker extends AccessEditChecker
{
    
    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool;
}
