<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;

interface AccessStreamChecker extends AccessChecker
{
    
    public function checkStream(User $user, ScopeData $data): bool;
}
