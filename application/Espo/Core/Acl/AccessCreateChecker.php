<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;

interface AccessCreateChecker extends AccessChecker
{
    
    public function checkCreate(User $user, ScopeData $data): bool;
}
