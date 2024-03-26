<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface AccessEntityReadChecker extends AccessReadChecker
{
    
    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool;
}
