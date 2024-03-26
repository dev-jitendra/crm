<?php


namespace Espo\Core\Acl\Table;

interface RoleListProvider
{
    
    public function get(): array;
}
