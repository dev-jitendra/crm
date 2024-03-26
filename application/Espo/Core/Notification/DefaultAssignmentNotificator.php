<?php


namespace Espo\Core\Notification;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Entities\Notification;
use Espo\Core\Notification\AssignmentNotificator\Params;


class DefaultAssignmentNotificator implements AssignmentNotificator
{
    public function __construct(
        protected User $user,
        protected EntityManager $entityManager,
        protected UserEnabledChecker $userChecker
    ) {}

    public function process(Entity $entity, Params $params): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        if ($entity->hasLinkMultipleField('assignedUsers')) {
            
            $userIdList = $entity->getLinkMultipleIdList('assignedUsers');
            
            $fetchedAssignedUserIdList = $entity->getFetched('assignedUsersIds') ?? [];

            foreach ($userIdList as $userId) {
                if (in_array($userId, $fetchedAssignedUserIdList)) {
                    continue;
                }

                $this->processForUser($entity, $userId);
            }

            return;
        }

        if (!$entity->get('assignedUserId')) {
            return;
        }

        if (!$entity->isAttributeChanged('assignedUserId')) {
            return;
        }

        $assignedUserId = $entity->get('assignedUserId');

        $this->processForUser($entity, $assignedUserId);
    }

    protected function processForUser(Entity $entity, string $assignedUserId): void
    {
        if (!$this->userChecker->checkAssignment($entity->getEntityType(), $assignedUserId)) {
            return;
        }

        if ($entity->hasAttribute('createdById') && $entity->hasAttribute('modifiedById')) {
            $isSelfAssignment = $entity->isNew() ?
                $assignedUserId === $entity->get('createdById') :
                $assignedUserId === $entity->get('modifiedById');

            if ($isSelfAssignment) {
                return;
            }
        }

        $isSelfAssignment = $assignedUserId === $this->user->getId();

        if ($isSelfAssignment) {
            return;
        }

        $this->entityManager->createEntity(Notification::ENTITY_TYPE, [
            'type' => Notification::TYPE_ASSIGN,
            'userId' => $assignedUserId,
            'data' => [
                'entityType' => $entity->getEntityType(),
                'entityId' => $entity->getId(),
                'entityName' => $entity->get('name'),
                'isNew' => $entity->isNew(),
                'userId' => $this->user->getId(),
                'userName' => $this->user->getName(),
            ],
            'relatedType' => $entity->getEntityType(),
            'relatedId' => $entity->getId(),
        ]);
    }
}
