<?php


namespace Espo\Classes\Jobs;

use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Entities\Job;
use Espo\Core\Job\JobDataLess;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Config;

use DateTime;
use DateTimeZone;

class CheckNewVersion implements JobDataLess
{
    
    protected $config;
    
    protected $entityManager;

    public function __construct(Config $config, EntityManager $entityManager)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function run(): void
    {
        if (
            !$this->config->get('adminNotifications') ||
            !$this->config->get('adminNotificationsNewVersion')
        ) {
            return;
        }

        $className = \Espo\Tools\AdminNotifications\Jobs\CheckNewVersion::class;

        
        $this->entityManager->createEntity(Job::ENTITY_TYPE, [
            'name' => $className,
            'className' => $className,
            'executeTime' => $this->getRunTime(),
        ]);
    }

    protected function getRunTime(): string
    {
        $hour = rand(0, 4);
        $minute = rand(0, 59);

        $nextDay = new DateTime('+ 1 day');
        $time = $nextDay->format(DateTimeUtil::SYSTEM_DATE_FORMAT) . ' ' . $hour . ':' . $minute . ':00';

        $timeZone = $this->config->get('timeZone');

        if (empty($timeZone)) {
            $timeZone = 'UTC';
        }

        $datetime = new DateTime($time, new DateTimeZone($timeZone));

        return $datetime
            ->setTimezone(new DateTimeZone('UTC'))
            ->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
    }

    
    protected function getEntityManager() 
    {
        return $this->entityManager;
    }
}
