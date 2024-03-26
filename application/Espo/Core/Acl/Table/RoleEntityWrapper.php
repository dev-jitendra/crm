<?php


namespace Espo\Core\Acl\Table;

use Espo\ORM\Entity;

use stdClass;

class RoleEntityWrapper implements Role
{
    private $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getScopeTableData(): stdClass
    {
        return $this->entity->get('data') ?? (object) [];
    }

    public function getFieldTableData(): stdClass
    {
        return $this->entity->get('fieldData') ?? (object) [];
    }

    public function getPermissionLevel(string $permission): ?string
    {
        return $this->entity->get($permission . 'Permission');
    }
}
