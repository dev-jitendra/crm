<?php


namespace Espo\Controllers;

use Espo\Core\Acl\Table;
use Espo\Core\Controllers\Record;

class Portal extends Record
{
    protected function checkAccess(): bool
    {
        $level = $this->acl->getPermissionLevel('portal');

        return $level === Table::LEVEL_YES;
    }
}
