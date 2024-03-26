<?php


namespace Espo\Modules\Crm\Tools\Activities;

use DateInterval;
use DateTime;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\Modules\Crm\Entities\Reminder;
use Espo\Modules\Crm\Entities\Task;
use Espo\ORM\EntityManager;
use Espo\Tools\PopupNotification\Item;
use Espo\Tools\PopupNotification\Provider;
use Exception;

class PopupNotificationsProvider implements Provider
{
    private const REMINDER_PAST_HOURS = 24;

    private Config $config;
    private EntityManager $entityManager;

    public function __construct(
        Config $config,
        EntityManager $entityManager
    ) {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    
    public function get(User $user): array
    {
        $userId = $user->getId();

        $dt = new DateTime();

        $pastHours = $this->config->get('reminderPastHours', self::REMINDER_PAST_HOURS);

        $now = $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

        $nowShifted = $dt
            ->sub(new DateInterval('PT' . $pastHours . 'H'))
            ->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);

        
        $reminderCollection = $this->entityManager
            ->getRDBRepositoryByClass(Reminder::class)
            ->select([
                'id',
                'entityType',
                'entityId',
            ])
            ->where([
                'type' => Reminder::TYPE_POPUP,
                'userId' => $userId,
                'remindAt<=' => $now,
                'startAt>' => $nowShifted,
            ])
            ->find();

        $resultList = [];

        foreach ($reminderCollection as $reminder) {
            $reminderId = $reminder->getId();
            $entityType = $reminder->getTargetEntityType();
            $entityId = $reminder->getTargetEntityId();

            if (!$entityId || !$entityType) {
                continue;
            }

            $entity = $this->entityManager->getEntityById($entityType, $entityId);

            if (!$entity) {
                continue;
            }

            $data = null;

            if (
                $entity instanceof CoreEntity &&
                $entity->hasLinkMultipleField('users')
            ) {
                $entity->loadLinkMultipleField('users', ['status' => 'acceptanceStatus']);

                $status = $entity->getLinkMultipleColumn('users', 'status', $userId);

                if ($status === Meeting::ATTENDEE_STATUS_DECLINED) {
                    $this->removeReminder($reminderId);

                    continue;
                }
            }

            $dateAttribute = $entityType === Task::ENTITY_TYPE ?
                'dateEnd' :
                'dateStart';

            $data = (object) [
                'id' => $entity->getId(),
                'entityType' => $entityType,
                $dateAttribute => $entity->get($dateAttribute),
                'name' => $entity->get('name'),
            ];

            $resultList[] = new Item($reminderId, $data);
        }

        return $resultList;
    }

    private function removeReminder(string $id): void
    {
        $deleteQuery = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(Reminder::ENTITY_TYPE)
            ->where(['id' => $id])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($deleteQuery);
    }
}
