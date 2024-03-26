<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;

interface AccessChecker
{
    
    public function check(User $user, ScopeData $data): bool;
}
