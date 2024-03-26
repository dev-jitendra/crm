<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface AccessEntityStreamChecker extends AccessStreamChecker
{
    
    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool;
}
