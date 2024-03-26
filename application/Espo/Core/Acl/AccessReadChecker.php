<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;

interface AccessReadChecker extends AccessChecker
{
    
    public function checkRead(User $user, ScopeData $data): bool;
}
