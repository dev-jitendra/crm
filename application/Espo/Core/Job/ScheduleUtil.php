<?php


namespace Espo\Core\Job;

use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\ORM\Collection;
use Espo\ORM\EntityManager;
use Espo\Entities\ScheduledJob as ScheduledJobEntity;
use Espo\Entities\ScheduledJobLogRecord as ScheduledJobLogRecordEntity;

class ScheduleUtil
{
    public function __construct(private EntityManager $entityManager)
    {}

    
    public function getActiveScheduledJobList(): Collection
    {
        
        $collection = $this->entityManager
            ->getRDBRepository(ScheduledJobEntity::ENTITY_TYPE)
            ->select([
                'id',
                'scheduling',
                'job',
                'name',
            ])
            ->where([
                'status' => ScheduledJobEntity::STATUS_ACTIVE,
            ])
            ->find();

        return $collection;
    }

    
    public function addLogRecord(
        string $scheduledJobId,
        string $status,
        ?string $runTime = null,
        ?string $targetId = null,
        ?string $targetType = null
    ): void {

        if (!isset($runTime)) {
            $runTime = date(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
        }

        
        $scheduledJob = $this->entityManager->getEntity(ScheduledJobEntity::ENTITY_TYPE, $scheduledJobId);

        if (!$scheduledJob) {
            return;
        }

        $scheduledJob->set('lastRun', $runTime);

        $this->entityManager->saveEntity($scheduledJob, [SaveOption::SILENT => true]);

        $scheduledJobLog = $this->entityManager->getNewEntity(ScheduledJobLogRecordEntity::ENTITY_TYPE);

        $scheduledJobLog->set([
            'scheduledJobId' => $scheduledJobId,
            'name' => $scheduledJob->getName(),
            'status' => $status,
            'executionTime' => $runTime,
            'targetId' => $targetId,
            'targetType' => $targetType,
        ]);

        $this->entityManager->saveEntity($scheduledJobLog);
    }
}
