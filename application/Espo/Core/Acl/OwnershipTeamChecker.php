<?php


namespace Espo\Core\Acl;

use Espo\ORM\Entity;
use Espo\Entities\User;


interface OwnershipTeamChecker extends OwnershipChecker
{
    
    public function checkTeam(User $user, Entity $entity): bool;
}
