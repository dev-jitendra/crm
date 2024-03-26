<?php


namespace Espo\Classes\Acl\Webhook;

use Espo\Entities\User;
use Espo\Entities\Webhook;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Traits\DefaultAccessCheckerDependency;


class AccessChecker implements AccessEntityCREDChecker
{
    use DefaultAccessCheckerDependency;

    public function __construct(DefaultAccessChecker $defaultAccessChecker)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
    }

    public function check(User $user, ScopeData $data): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->isApi()) {
            return false;
        }

        if ($data->isFalse()) {
            return false;
        }

        return true;
    }

    public function checkEntityCreate(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntityInternal($user, $entity, $data);
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntityInternal($user, $entity, $data);
    }

    public function checkEntityEdit(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntityInternal($user, $entity, $data);
    }

    public function checkEntityDelete(User $user, Entity $entity, ScopeData $data): bool
    {
        return $this->checkEntityInternal($user, $entity, $data);
    }

    private function checkEntityInternal(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($data->isFalse()) {
            return false;
        }

        if ($user->isApi() && $user->getId() === $entity->get('userId')) {
            return true;
        }

        return false;
    }
}
