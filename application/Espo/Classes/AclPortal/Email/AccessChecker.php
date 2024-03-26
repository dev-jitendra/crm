<?php


namespace Espo\Classes\AclPortal\Email;

use Espo\Entities\Email;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Acl\AccessEntityCREDSChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Acl\Table;
use Espo\Core\Portal\Acl\DefaultAccessChecker;
use Espo\Core\Portal\Acl\Traits\DefaultAccessCheckerDependency;


class AccessChecker implements AccessEntityCREDSChecker
{
    use DefaultAccessCheckerDependency;

    public function __construct(
        DefaultAccessChecker $defaultAccessChecker
    ) {
        $this->defaultAccessChecker = $defaultAccessChecker;
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if ($this->defaultAccessChecker->checkEntityRead($user, $entity, $data)) {
            return true;
        }

        if ($data->isFalse()) {
            return false;
        }

        if ($data->getRead() === Table::LEVEL_NO) {
            return false;
        }

        assert($entity instanceof CoreEntity);

        $userIdList = $entity->getLinkMultipleIdLIst('users');

        if (is_array($userIdList) && in_array($user->getId(), $userIdList)) {
            return true;
        }

        return false;
    }
}
