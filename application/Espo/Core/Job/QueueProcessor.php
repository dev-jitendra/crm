<?php


namespace Espo\Core\Job;

use Espo\Entities\Job as JobEntity;
use Espo\Core\Job\QueueProcessor\Params;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\System;
use Espo\Core\Job\Job\Status;

use Spatie\Async\Pool as AsyncPool;

class QueueProcessor
{
    private bool $noTableLocking;

    public function __construct(
        private QueueUtil $queueUtil,
        private JobRunner $jobRunner,
        private AsyncPoolFactory $asyncPoolFactory,
        private EntityManager $entityManager,
        ConfigDataProvider $configDataProvider
    ) {
        $this->noTableLocking = $configDataProvider->noTableLocking();
    }

    public function process(Params $params): void
    {
        $pool = $params->useProcessPool() ?
            $this->asyncPoolFactory->create() :
            null;

        $pendingJobList = $this->queueUtil->getPendingJobList(
            $params->getQueue(),
            $params->getGroup(),
            $params->getLimit()
        );

        foreach ($pendingJobList as $job) {
            $this->processJob($params, $job, $pool);
        }

        $pool?->wait();
    }

    private function processJob(Params $params, JobEntity $job, ?AsyncPool $pool = null): void
    {
        $noLock = $params->noLock();
        $lockTable = $job->getScheduledJobId() && !$noLock && !$this->noTableLocking;

        if ($lockTable) {
            
            $this->entityManager->getLocker()->lockExclusive(JobEntity::ENTITY_TYPE);
        }

        $skip = !$noLock && !$this->queueUtil->isJobPending($job->getId());

        if (
            !$skip &&
            $job->getScheduledJobId() &&
            $this->queueUtil->isScheduledJobRunning(
                $job->getScheduledJobId(),
                $job->getTargetId(),
                $job->getTargetType(),
                $job->getTargetGroup()
            )
        ) {
            $skip = true;
        }

        if ($skip && $lockTable) {
            $this->entityManager->getLocker()->rollback();
        }

        if ($skip) {
            return;
        }

        $job->setStartedAtNow();

        if ($pool) {
            $job->setStatus(Status::READY);
        }
        else {
            $job->setStatus(Status::RUNNING);
            $job->setPid(System::getPid());
        }

        $this->entityManager->saveEntity($job);

        if ($lockTable) {
            $this->entityManager->getLocker()->commit();
        }

        if (!$pool) {
            $this->jobRunner->run($job);

            return;
        }

        $task = new JobTask($job->getId());

        $pool->add($task);
    }
}
