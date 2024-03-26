<?php


namespace Espo\Core\Repositories;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Modules\Crm\Entities\Meeting;
use Espo\Modules\Crm\Entities\Reminder;
use Espo\ORM\Entity;
use Espo\Core\Di;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use DateTime;
use DateTimeZone;
use RuntimeException;
use Exception;


class Event extends Database implements

    Di\DateTimeAware,
    Di\ConfigAware
{
    use Di\DateTimeSetter;
    use Di\ConfigSetter;

    
    protected $reminderSkippingStatusList = [
        Meeting::STATUS_HELD,
        Meeting::STATUS_NOT_HELD,
    ];

    
    protected function beforeSave(Entity $entity, array $options = [])
    {
        if (
            $entity->isAttributeChanged('status') &&
            in_array($entity->get('status'), $this->reminderSkippingStatusList)
        ) {
            $entity->set('reminders', []);
        }

        if ($entity->has('dateStartDate')) {
            $dateStartDate = $entity->get('dateStartDate');

            if (!empty($dateStartDate)) {
                $dateStart = $dateStartDate . ' 00:00:00';

                $dateStart = $this->convertDateTimeToDefaultTimezone($dateStart);

                $entity->set('dateStart', $dateStart);
            }
            else {
                $entity->set('dateStartDate', null);
            }
        }

        if ($entity->has('dateEndDate')) {
            $dateEndDate = $entity->get('dateEndDate');

            if (!empty($dateEndDate)) {
                try {
                    $dt = new DateTime(
                        $this->convertDateTimeToDefaultTimezone($dateEndDate . ' 00:00:00')
                    );
                } catch (Exception) {
                    throw new RuntimeException("Bad date-time.");
                }

                $dt->modify('+1 day');

                $dateEnd = $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
                $entity->set('dateEnd', $dateEnd);
            }
            else {
                $entity->set('dateEndDate', null);
            }
        }

        parent::beforeSave($entity, $options);
    }

    
    protected function afterRemove(Entity $entity, array $options = [])
    {
        parent::afterRemove($entity, $options);

        $delete = $this->entityManager->getQueryBuilder()
            ->delete()
            ->from(Reminder::ENTITY_TYPE)
            ->where([
                'entityId' => $entity->getId(),
                'entityType' => $entity->getEntityType(),
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);
    }

    
    protected function convertDateTimeToDefaultTimezone($string)
    {
        $timeZone = $this->config->get('timeZone') ?? 'UTC';

        $tz = new DateTimeZone($timeZone);

        $dt = DateTime::createFromFormat(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT, $string, $tz);

        if ($dt === false) {
            throw new RuntimeException("Could not parse date-time `{$string}`.");
        }

        $utcTz = new DateTimeZone('UTC');

        return $dt
            ->setTimezone($utcTz)
            ->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
    }
}
