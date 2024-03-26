<?php


namespace Espo\Tools\Notification;

use Espo\Core\Notification\AssignmentNotificatorFactory;
use Espo\Core\Notification\AssignmentNotificator;
use Espo\Core\Notification\AssignmentNotificator\Params as AssignmentNotificatorParams;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Config;
use Espo\Tools\Stream\Service as StreamService;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\Entities\User;
use Espo\Entities\Notification;
use Espo\Core\ORM\Entity as CoreEntity;


class HookProcessor
{
    
    private $notificatorsHash = [];
    
    private $hasStreamCache = [];
    
    private $userNameHash = [];

    public function __construct(
        private Metadata $metadata,
        private Config $config,
        private EntityManager $entityManager,
        private StreamService $streamService,
        private AssignmentNotificatorFactory $notificatorFactory,
        private User $user
    ) {}

    
    public function afterSave(Entity $entity, array $options): void
    {
        $entityType = $entity->getEntityType();

        if (!$entity instanceof CoreEntity) {
            return;
        }

        
        if (
            $this->checkHasStream($entityType) &&
            !$entity->hasLinkMultipleField('assignedUsers') &&
            !$this->forceAssignmentNotificator($entityType)
        ) {
            return;
        }

        $assignmentNotificationsEntityList = $this->config->get('assignmentNotificationsEntityList') ?? [];

        if (!in_array($entityType, $assignmentNotificationsEntityList)) {
            return;
        }

        $notificator = $this->getNotificator($entityType);

        if (!$notificator instanceof AssignmentNotificator) {
            
            $notificator->process($entity, $options);

            return;
        }

        $params = AssignmentNotificatorParams::create()->withRawOptions($options);

        $notificator->process($entity, $params);
    }

    
    public function beforeRemove(Entity $entity, array $options): void
    {
        $entityType = $entity->getEntityType();

        if (!$this->checkHasStream($entityType)) {
            return;
        }

        $followersData = $this->streamService->getEntityFollowers($entity);

        $userIdList = $followersData['idList'];

        $removedById = $options['modifiedById'] ?? $this->user->getId();
        $removedByName = $this->getUserNameById($removedById);

        foreach ($userIdList as $userId) {
            if ($userId === $removedById) {
                continue;
            }

            $this->entityManager->createEntity(Notification::ENTITY_TYPE, [
                'userId' => $userId,
                'type' => Notification::TYPE_ENTITY_REMOVED,
                'data' => [
                    'entityType' => $entity->getEntityType(),
                    'entityId' => $entity->getId(),
                    'entityName' => $entity->get('name'),
                    'userId' => $removedById,
                    'userName' => $removedByName,
                ],
            ]);
        }
    }

    public function afterRemove(Entity $entity): void
    {
        $query = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(Notification::ENTITY_TYPE)
            ->where([
                'OR' => [
                    [
                        'relatedId' => $entity->getId(),
                        'relatedType' => $entity->getEntityType(),
                    ],
                    [
                        'relatedParentId' => $entity->getId(),
                        'relatedParentType' => $entity->getEntityType(),
                    ],
                ],
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($query);
    }

    private function checkHasStream(string $entityType): bool
    {
        if (!array_key_exists($entityType, $this->hasStreamCache)) {
            $this->hasStreamCache[$entityType] =
                (bool) $this->metadata->get(['scopes', $entityType, 'stream']);
        }

        return $this->hasStreamCache[$entityType];
    }

    
    private function getNotificator(string $entityType): object
    {
        if (empty($this->notificatorsHash[$entityType])) {
            $notificator = $this->notificatorFactory->create($entityType);

            $this->notificatorsHash[$entityType] = $notificator;
        }

        return $this->notificatorsHash[$entityType];
    }

    private function getUserNameById(string $id): string
    {
        if ($id === $this->user->getId()) {
            return $this->user->get('name');
        }

        if (!array_key_exists($id, $this->userNameHash)) {
            
            $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $id);

            if ($user) {
                $this->userNameHash[$id] = $user->getName() ?? $id;
            }
        }

        return $this->userNameHash[$id];
    }

    private function forceAssignmentNotificator(string $entityType): bool
    {
        return (bool) $this->metadata->get(['notificationDefs', $entityType, 'forceAssignmentNotificator']);
    }
}
