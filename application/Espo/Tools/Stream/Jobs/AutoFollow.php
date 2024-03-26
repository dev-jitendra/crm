<?php


namespace Espo\Tools\Stream\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;

use Espo\Core\AclManager;
use Espo\Core\Acl\Exceptions\NotImplemented as AclNotImplemented;

use Espo\Entities\Note;
use Espo\ORM\Collection;
use Espo\ORM\EntityManager;

use Espo\Tools\Stream\Service as Service;
use Espo\Tools\Notification\Service as NotificationService;

use Espo\Entities\User;


class AutoFollow implements Job
{
    private Service $service;
    private NotificationService $notificationService;
    private AclManager $aclManager;
    private EntityManager $entityManager;

    public function __construct(
        Service $service,
        NotificationService $notificationService,
        AclManager $aclManager,
        EntityManager $entityManager
    ) {
        $this->service = $service;
        $this->notificationService = $notificationService;
        $this->aclManager = $aclManager;
        $this->entityManager = $entityManager;
    }

    public function run(Data $data): void
    {
        
        $userIdList = $data->get('userIdList') ?? [];
        $entityType = $data->get('entityType');
        $entityId = $data->get('entityId');

        if (!$entityId || !$entityType) {
            return;
        }

        $entity = $this->entityManager->getEntityById($entityType, $entityId);

        if (!$entity) {
            return;
        }

        foreach ($userIdList as $i => $userId) {
            
            $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

            if (!$user) {
                unset($userIdList[$i]);

                continue;
            }

            try {
                $hasAccess = $this->aclManager->checkEntityStream($user, $entity);
            }
            catch (AclNotImplemented $e) {
                $hasAccess = false;
            }

            if (!$hasAccess) {
                unset($userIdList[$i]);
            }
        }

        $userIdList = array_values($userIdList);

        foreach ($userIdList as $i => $userId) {
            if ($this->service->checkIsFollowed($entity, $userId)) {
                unset($userIdList[$i]);
            }
        }

        $userIdList = array_values($userIdList);

        if (!count($userIdList)) {
            return;
        }

        $this->service->followEntityMass($entity, $userIdList);

        
        $noteList = $this->entityManager
            ->getRDBRepository(Note::ENTITY_TYPE)
            ->where([
                'parentType' => $entityType,
                'parentId' => $entityId,
            ])
            ->order('number', 'ASC')
            ->find();

        foreach ($noteList as $note) {
            $this->notificationService->notifyAboutNote($userIdList, $note);
        }
    }
}
