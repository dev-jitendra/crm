<?php


namespace Espo\Core\Acl;

use Espo\Entities\User;
use Espo\ORM\Entity;


interface LinkChecker
{
    
    public function check(User $user, Entity $entity, Entity $foreignEntity): bool;
}
