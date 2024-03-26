<?php


namespace Espo\Classes\Cleanup;

use Espo\Core\Cleanup\Cleanup;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Modules\Crm\Entities\Reminder;
use Espo\ORM\EntityManager;

use DateTime;

class Reminders implements Cleanup
{
    private string $cleanupRemindersPeriod = '15 days';

    private Config $config;
    private EntityManager $entityManager;

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function process(): void
    {
        $period = '-' . $this->config->get('cleanupRemindersPeriod', $this->cleanupRemindersPeriod);

        $dt = new DateTime();

        $dt->modify($period);

        $delete = $this->entityManager
            ->getQueryBuilder()
            ->delete()
            ->from(Reminder::ENTITY_TYPE)
            ->where([
                'remindAt<' => $dt->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT),
            ])
            ->build();

        $this->entityManager->getQueryExecutor()->execute($delete);
    }
}
