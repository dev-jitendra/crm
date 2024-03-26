<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;

interface AccessDeleteChecker extends AccessChecker
{
    
    public function checkDelete(User $user, ScopeData $data): bool;
}
