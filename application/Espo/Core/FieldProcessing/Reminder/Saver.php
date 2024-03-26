<?php


namespace Espo\Core\FieldProcessing\Reminder;

use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Core\Utils\Id\RecordIdGenerator;
use Espo\Modules\Crm\Entities\Reminder;
use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\FieldProcessing\Saver as SaverInterface;
use Espo\Core\FieldProcessing\Saver\Params;
use Espo\Core\ORM\EntityManager;

use stdClass;
use DateInterval;
use DateTime;


class Saver implements SaverInterface
{
    protected string $dateAttribute = 'dateStart';

    public function __construct(
        private EntityManager $entityManager,
        private RecordIdGenerator $idGenerator
    ) {}

    
    public function process(Entity $entity, Params $params): void
    {
        $entityType = $entity->getEntityType();

        $hasReminder = $this->entityManager
            ->getDefs()
            ->getEntity($entityType)
            ->hasField('reminders');

        if (!$hasReminder) {
            return;
        }

        $dateAttribute = $this->entityManager
            ->getDefs()
            ->getEntity($entityType)
            ->getField('reminders')
            ->getParam('dateField') ??
            $this->dateAttribute;

        $toProcess =
            $entity->isNew() ||
            $entity->isAttributeChanged('assignedUserId') ||
            ($entity->hasLinkMultipleField('assignedUsers') && $entity->isAttributeChanged('assignedUsersIds')) ||
            ($entity->hasLinkMultipleField('users') && $entity->isAttributeChanged('usersIds')) ||
            $entity->isAttributeChanged($dateAttribute) ||
            $entity->has('reminders');

        if (!$toProcess) {
            return;
        }

        $reminderTypeList = $this->entityManager
            ->getDefs()
            ->getEntity(Reminder::ENTITY_TYPE)
            ->getField('type')
            ->getParam('options') ?? [];

        $reminderList = $entity->has('reminders') ?
            $entity->get('reminders') :
            $this->getEntityReminderDataList($entity);

        if (!$entity->isNew()) {
            $query = $this->entityManager
                ->getQueryBuilder()
                ->delete()
                ->from(Reminder::ENTITY_TYPE)
                ->where([
                    'entityId' => $entity->getId(),
                    'entityType' => $entityType,
                    'deleted' => false,
                ])
                ->build();

            $this->entityManager->getQueryExecutor()->execute($query);
        }

        if (empty($reminderList)) {
            return;
        }

        $dateValue = $entity->get($dateAttribute);

        if (!$entity->has($dateAttribute)) {
            $reloadedEntity = $this->entityManager->getEntity($entityType, $entity->getId());

            if ($reloadedEntity) {
                $dateValue = $reloadedEntity->get($dateAttribute);
            }
        }

        if (!$dateValue) {
            return;
        }

        if ($entity->hasLinkMultipleField('users')) {
            $userIdList = $entity->getLinkMultipleIdList('users');
        }
        else if ($entity->hasLinkMultipleField('assignedUsers')) {
            $userIdList = $entity->getLinkMultipleIdList('assignedUsers');
        }
        else {
            $userIdList = [];

            if ($entity->get('assignedUserId')) {
                $userIdList[] = $entity->get('assignedUserId');
            }
        }

        if (empty($userIdList)) {
            return;
        }

        $dateValueObj = new DateTime($dateValue);

        foreach ($reminderList as $item) {
            $remindAt = clone $dateValueObj;
            $seconds = intval($item->seconds);
            $type = $item->type;

            if (!in_array($type , $reminderTypeList)) {
                continue;
            }

            $remindAt->sub(new DateInterval('PT' . $seconds . 'S'));

            foreach ($userIdList as $userId) {
                $reminderId = $this->idGenerator->generate();

                $query = $this->entityManager
                    ->getQueryBuilder()
                    ->insert()
                    ->into(Reminder::ENTITY_TYPE)
                    ->columns([
                        'id',
                        'entityId',
                        'entityType',
                        'type',
                        'userId',
                        'remindAt',
                        'startAt',
                        'seconds',
                    ])
                    ->values([
                        'id' => $reminderId,
                        'entityId' => $entity->getId(),
                        'entityType' => $entityType,
                        'type' => $type,
                        'userId' => $userId,
                        'remindAt' => $remindAt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT),
                        'startAt' => $dateValue,
                        'seconds' => $seconds,
                    ])
                    ->build();

                $this->entityManager->getQueryExecutor()->execute($query);
            }
        }
    }

    
    private function getEntityReminderDataList(Entity $entity): array
    {
        $reminderDataList = [];

        $reminderCollection = $this->entityManager
            ->getRDBRepository(Reminder::ENTITY_TYPE)
            ->select(['seconds', 'type'])
            ->where([
                'entityType' => $entity->getEntityType(),
                'entityId' => $entity->getId(),
            ])
            ->distinct()
            ->order('seconds')
            ->find();

        foreach ($reminderCollection as $reminder) {
            $reminderDataList[] = (object) [
                'seconds' => $reminder->get('seconds'),
                'type' => $reminder->get('type'),
            ];
        }

        return $reminderDataList;
    }
}
