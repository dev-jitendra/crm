<?php


namespace Espo\Core\Acl\Table;

use stdClass;

interface Role
{
    public function getScopeTableData(): stdClass;

    public function getFieldTableData(): stdClass;

    public function getPermissionLevel(string $permission): ?string;
}
