<?php


namespace Espo\Core\Portal\Acl\Table;

use Espo\ORM\EntityManager;

use Espo\Entities\Portal;
use Espo\Entities\PortalRole;
use Espo\Entities\User;
use Espo\Core\Acl\Table\Role;
use Espo\Core\Acl\Table\RoleEntityWrapper;
use Espo\Core\Acl\Table\RoleListProvider as RoleListProviderInterface;

class RoleListProvider implements RoleListProviderInterface
{
    public function __construct(
        private User $user,
        private Portal $portal,
        private EntityManager $entityManager
    ) {}

    
    public function get(): array
    {
        $roleList = [];

        
        $userRoleList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->getRelation($this->user, 'portalRoles')
            ->find();

        foreach ($userRoleList as $role) {
            $roleList[] = $role;
        }

        
        $portalRoleList = $this->entityManager
            ->getRDBRepository(Portal::ENTITY_TYPE)
            ->getRelation($this->portal, 'portalRoles')
            ->find();

        foreach ($portalRoleList as $role) {
            $roleList[] = $role;
        }

        return array_map(
            function (PortalRole $role): RoleEntityWrapper {
                return new RoleEntityWrapper($role);
            },
            $roleList
        );
    }
}
