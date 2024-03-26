<?php


namespace Espo\Core\Di;

use Espo\Entities\User;

trait UserSetter
{
    
    protected $user;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
