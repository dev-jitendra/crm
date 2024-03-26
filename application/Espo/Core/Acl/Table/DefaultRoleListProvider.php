<?php


namespace Espo\Core\Acl\Table;

use Espo\Entities\Team;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Entities\Role as RoleEntity;

class DefaultRoleListProvider implements RoleListProvider
{
    public function __construct(private User $user, private EntityManager $entityManager)
    {}

    
    public function get(): array
    {
        $roleList = [];

        
        $userRoleList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->getRelation($this->user, 'roles')
            ->find();

        foreach ($userRoleList as $role) {
            $roleList[] = $role;
        }

        
        $teamList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->getRelation($this->user, 'teams')
            ->find();

        foreach ($teamList as $team) {
            
            $teamRoleList = $this->entityManager
                ->getRDBRepository(Team::ENTITY_TYPE)
                ->getRelation($team, 'roles')
                ->find();

            foreach ($teamRoleList as $role) {
                $roleList[] = $role;
            }
        }

        return array_map(
            function (RoleEntity $role): RoleEntityWrapper {
                return new RoleEntityWrapper($role);
            },
            $roleList
        );
    }
}
