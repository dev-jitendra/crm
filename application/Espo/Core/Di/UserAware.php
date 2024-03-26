<?php


namespace Espo\Core\Di;

use Espo\Entities\User;

interface UserAware
{
    public function setUser(User $user): void;
}
