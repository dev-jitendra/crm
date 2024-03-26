<?php


namespace Espo\Modules\Crm\Classes\Acl\MassEmail;

use Espo\Entities\User;
use Espo\Modules\Crm\Entities\MassEmail;
use Espo\ORM\Entity;
use Espo\Core\Acl\OwnershipOwnChecker;
use Espo\Core\Acl\OwnershipTeamChecker;
use Espo\Core\AclManager;
use Espo\Core\ORM\EntityManager;


class OwnershipChecker implements OwnershipOwnChecker, OwnershipTeamChecker
{
    public function __construct(
        private AclManager $aclManager,
        private EntityManager $entityManager
    ) {}

    public function checkOwn(User $user, Entity $entity): bool
    {
        $campaignId = $entity->get('campaignId');

        if (!$campaignId) {
            return false;
        }

        $campaign = $this->entityManager->getEntity('Campaign', $campaignId);

        if ($campaign && $this->aclManager->checkOwnershipOwn($user, $campaign)) {
            return true;
        }

        return false;
    }

    public function checkTeam(User $user, Entity $entity): bool
    {
        $campaignId = $entity->get('campaignId');

        if (!$campaignId) {
            return false;
        }

        $campaign = $this->entityManager->getEntity('Campaign', $campaignId);

        if ($campaign && $this->aclManager->checkOwnershipTeam($user, $campaign)) {
            return true;
        }

        return false;
    }
}
