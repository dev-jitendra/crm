<?php


namespace Espo\Core\Acl\Table;

use Espo\Entities\User;

use Espo\Core\Acl\Table;

interface TableFactory
{
    
    public function create(User $user): Table;
}
