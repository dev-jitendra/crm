<?php


namespace Espo\Modules\Crm\Classes\AclPortal\KnowledgeBaseArticle;

use Espo\Entities\User;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Acl\AccessEntityCREDChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\Portal\Acl\DefaultAccessChecker;
use Espo\Core\Portal\Acl\Traits\DefaultAccessCheckerDependency;


class AccessChecker implements AccessEntityCREDChecker
{
    use DefaultAccessCheckerDependency;

    public function __construct(DefaultAccessChecker $defaultAccessChecker)
    {
        $this->defaultAccessChecker = $defaultAccessChecker;
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        if (!$this->defaultAccessChecker->checkEntityRead($user, $entity, $data)) {
            return false;
        }

        if ($entity->get('status') !== KnowledgeBaseArticle::STATUS_PUBLISHED) {
            return false;
        }

        assert($entity instanceof CoreEntity);

        $portalIdList = $entity->getLinkMultipleIdList('portals');

        $portalId = $user->get('portalId');

        if (!$portalId) {
            return false;
        }

        return in_array($portalId, $portalIdList);
    }
}
