<?php


namespace Espo\Modules\Crm\Classes\Acl\Meeting;

use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Acl\AccessEntityCREDSChecker;
use Espo\Core\Acl\DefaultAccessChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;
use Espo\Core\Acl\Traits\DefaultAccessCheckerDependency;


class AccessChecker implements AccessEntityCREDSChecker
{
    use DefaultAccessCheckerDependency;

    public function __construct(DefaultAccessChecker $defaultAccessChecker)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($this->defaultAccessChecker->checkEntityRead($user, $entity, $data)) {
            return true;
        }

        assert($entity instanceof CoreEntity);

        if ($data->getRead() === Table::LEVEL_OWN || $data->getRead() === Table::LEVEL_TEAM) {
            if ($entity->hasLinkMultipleId('users', $user->getId())) {
                return true;
            }
        }

        return false;
    }

    public function checkEntityStream(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($this->defaultAccessChecker->checkEntityStream($user, $entity, $data)) {
            return true;
        }

        assert($entity instanceof CoreEntity);

        if ($data->getStream() === Table::LEVEL_OWN || $data->getRead() === Table::LEVEL_TEAM) {
            if ($entity->hasLinkMultipleId('users', $user->getId())) {
                return true;
            }
        }

        return false;
    }
}
