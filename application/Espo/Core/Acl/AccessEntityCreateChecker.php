<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface AccessEntityCreateChecker extends AccessCreateChecker
{
    
    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool;
}
