<?php


namespace Espo\Modules\Crm\Classes\AssignmentNotificators;

use Espo\Core\Field\LinkParent;
use Espo\Core\Notification\AssignmentNotificator;
use Espo\Core\Notification\AssignmentNotificator\Params;
use Espo\Core\Notification\DefaultAssignmentNotificator;
use Espo\Core\Notification\UserEnabledChecker;
use Espo\Core\Utils\Metadata;
use Espo\Entities\Notification;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Call;
use Espo\Modules\Crm\Entities\Meeting as MeetingEntity;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;


class Meeting implements AssignmentNotificator
{
    private const ATTR_USERS_IDS = 'usersIds';
    private const NOTIFICATION_TYPE_EVENT_ATTENDEE = 'EventAttendee';

    private DefaultAssignmentNotificator $defaultAssignmentNotificator;
    private UserEnabledChecker $userEnabledChecker;
    private EntityManager $entityManager;
    private User $user;
    private Metadata $metadata;

    public function __construct(
        DefaultAssignmentNotificator $defaultAssignmentNotificator,
        UserEnabledChecker $userEnabledChecker,
        EntityManager $entityManager,
        User $user,
        Metadata $metadata
    ) {
        $this->defaultAssignmentNotificator = $defaultAssignmentNotificator;
        $this->userEnabledChecker = $userEnabledChecker;
        $this->entityManager = $entityManager;
        $this->user = $user;
        $this->metadata = $metadata;
    }

    
    public function process(Entity $entity, Params $params): void
    {
        
        if (!$this->hasStream($entity->getEntityType())) {
            $this->defaultAssignmentNotificator->process($entity, $params);
        }

        if ($entity->getStatus() !== MeetingEntity::STATUS_PLANNED) {
            return;
        }

        if (!$entity->isAttributeChanged(self::ATTR_USERS_IDS)) {
            return;
        }

        
        $prevIds = $entity->getFetched(self::ATTR_USERS_IDS) ?? [];
        $ids = $entity->getUsers()->getIdList();

        $newIds = array_filter($ids, fn ($id) => !in_array($id, $prevIds));

        $assignedUser = $entity->getAssignedUser();

        if ($assignedUser) {
            $newIds = array_filter($newIds, fn($id) => $id !== $assignedUser->getId());
        }

        $newIds = array_values($newIds);

        foreach ($newIds as $id) {
            $this->processForUser($entity, $id);
        }
    }

    
    private function processForUser(Entity $entity, string $userId): void
    {
        if (!$this->userEnabledChecker->checkAssignment($entity->getEntityType(), $userId)) {
            return;
        }

        $createdBy = $entity->getCreatedBy();
        $modifiedBy = $entity->getModifiedBy();

        $isSelfAssignment = $entity->isNew() ?
            $createdBy && $userId ===  $createdBy->getId() :
            $modifiedBy && $userId === $modifiedBy->getId();

        if ($isSelfAssignment) {
            return;
        }

        
        $notification = $this->entityManager->getRDBRepositoryByClass(Notification::class)->getNew();

        $notification
            ->setType(self::NOTIFICATION_TYPE_EVENT_ATTENDEE)
            ->setUserId($userId)
            ->setRelated(
                LinkParent::create($entity->getEntityType(), $entity->getId())
            )
            ->setData((object) [
                'entityType' => $entity->getEntityType(),
                'entityId' => $entity->getId(),
                'entityName' => $entity->getName(),
                'isNew' => $entity->isNew(),
                'userId' => $this->user->getId(),
                'userName' => $this->user->getName(),
            ]);

        $this->entityManager->saveEntity($notification);
    }

    private function hasStream(string $entityType): bool
    {
        return (bool) $this->metadata->get(['scopes', $entityType, 'stream']);
    }
}
