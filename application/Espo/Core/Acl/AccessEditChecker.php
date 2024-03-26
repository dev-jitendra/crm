<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;

interface AccessEditChecker extends AccessChecker
{
    
    public function checkEdit(User $user, ScopeData $data): bool;
}
