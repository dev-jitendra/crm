<?php


namespace Espo\Core\Utils\Acl;

use Espo\Core\Acl\Exceptions\NotAvailable;
use Espo\Entities\Portal;
use Espo\Entities\User;
use Espo\ORM\EntityManager;
use Espo\Core\AclManager;
use Espo\Core\Portal\AclManagerContainer as PortalAclManagerContainer;
use Espo\Core\ApplicationState;


class UserAclManagerProvider
{
    
    private $map = [];

    public function __construct(
        private EntityManager $entityManager,
        private AclManager $aclManager,
        private PortalAclManagerContainer $portalAclManagerContainer,
        private ApplicationState $applicationState
    ) {}

    
    public function get(User $user): AclManager
    {
        $key = $user->hasId() ? $user->getId() : spl_object_hash($user);

        if (!isset($this->map[$key])) {
            $this->map[$key] = $this->load($user);
        }

        return $this->map[$key];
    }

    
    private function load(User $user): AclManager
    {
        $aclManager = $this->aclManager;

        if ($user->isPortal() && !$this->applicationState->isPortal()) {
            
            $portal = $this->entityManager
                ->getRDBRepository(User::ENTITY_TYPE)
                ->getRelation($user, 'portals')
                ->findOne();

            if (!$portal) {
                throw new NotAvailable("No portal for portal user '" . $user->getId() . "'.");
            }

            $aclManager = $this->portalAclManagerContainer->get($portal);
        }

        return $aclManager;
    }
}
