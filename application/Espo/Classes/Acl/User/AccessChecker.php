<?php


namespace Espo\Classes\Acl\User;

use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDSChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;
use Espo\Core\Acl\Traits\DefaultAccessCheckerDependency;
use Espo\Core\AclManager;


class AccessChecker implements AccessEntityCREDSChecker
{
    use DefaultAccessCheckerDependency;

    private DefaultAccessChecker $defaultAccessChecker;
    private AclManager $aclManager;

    public function __construct(DefaultAccessChecker $defaultAccessChecker, AclManager $aclManager)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->aclManager = $aclManager;
    }

    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityCreate($user, $entity, $data);
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        

        if ($entity->isPortal()) {
            if ($this->aclManager->getPermissionLevel($user, 'portal') === Table::LEVEL_YES) {
                return true;
            }

            return false;
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityRead($user, $entity, $data);
    }

    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        

        if ($entity->isSystem()) {
            return false;
        }

        if (!$user->isAdmin()) {
            if ($user->getId() !== $entity->getId()) {
                return false;
            }
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityEdit($user, $entity, $data);
    }

    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        

        if (!$user->isAdmin()) {
            return false;
        }

        if ($entity->isSystem()) {
            return false;
        }

        if ($entity->isSuperAdmin() && !$user->isSuperAdmin()) {
            return false;
        }

        return $this->defaultAccessChecker->checkEntityDelete($user, $entity, $data);
    }

    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool
    {
        

        return $this->aclManager->checkUserPermission($user, $entity, 'user');
    }
}
