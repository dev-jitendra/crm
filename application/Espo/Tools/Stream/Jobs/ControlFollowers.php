<?php


namespace Espo\Tools\Stream\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;

use Espo\Core\AclManager;
use Espo\Core\Acl\Exceptions\NotImplemented as AclNotImplemented;

use Espo\ORM\EntityManager;

use Espo\Tools\Stream\Service as Service;
use Espo\Entities\User;


class ControlFollowers implements Job
{
    private Service $service;
    private AclManager $aclManager;
    private EntityManager $entityManager;

    public function __construct(
        Service $service,
        AclManager $aclManager,
        EntityManager $entityManager
    ) {
        $this->service = $service;
        $this->aclManager = $aclManager;
        $this->entityManager = $entityManager;
    }

    public function run(Data $data): void
    {
        $entityType = $data->get('entityType');
        $entityId = $data->get('entityId');

        if (!$entityId || !$entityType) {
            return;
        }

        $entity = $this->entityManager->getEntity($entityType, $entityId);

        if (!$entity) {
            return;
        }

        $idList = $this->service->getEntityFollowerIdList($entity);

        $userList = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'id' => $idList,
            ])
            ->find();

        foreach ($userList as $user) {
            
            $userId = $user->getId();

            if (!$user->isActive()) {
                $this->service->unfollowEntity($entity, $userId);

                continue;
            }

            if ($user->isPortal()) {
                continue;
            }

            try {
                $hasAccess = $this->aclManager->checkEntityStream($user, $entity);
            }
            catch (AclNotImplemented $e) {
                $hasAccess = false;
            }

            if ($hasAccess) {
                continue;
            }

            $this->service->unfollowEntity($entity, $userId);
        }
    }
}
