<?php


namespace Espo\Core\Rebuild\Actions;

use Espo\Core\Rebuild\RebuildAction;
use Espo\Core\Utils\Metadata;
use Espo\Entities\ScheduledJob;
use Espo\ORM\EntityManager;


class ScheduledJobs implements RebuildAction
{
    public function __construct(
        private Metadata $metadata,
        private EntityManager $entityManager
    ) {}

    public function process(): void
    {
        $jobDefs = array_merge(
            $this->metadata->get(['entityDefs', 'ScheduledJob', 'jobs'], []), 
            $this->metadata->get(['app', 'scheduledJobs'], [])
        );

        $systemJobNameList = [];

        foreach ($jobDefs as $jobName => $defs) {
            if (!$jobName) {
                continue;
            }

            if (empty($defs['isSystem']) || empty($defs['scheduling'])) {
                continue;
            }

            $systemJobNameList[] = $jobName;

            $sj = $this->entityManager
                ->getRDBRepository(ScheduledJob::ENTITY_TYPE)
                ->where([
                    'job' => $jobName,
                    'status' => ScheduledJob::STATUS_ACTIVE,
                    'scheduling' => $defs['scheduling'],
                ])
                ->findOne();

            if ($sj) {
                continue;
            }

            $existingJob = $this->entityManager
                ->getRDBRepository(ScheduledJob::ENTITY_TYPE)
                ->where([
                    'job' => $jobName,
                ])
                ->findOne();

            if ($existingJob) {
                $this->entityManager->removeEntity($existingJob);
            }

            $name = $jobName;

            if (!empty($defs['name'])) {
                $name = $defs['name'];
            }

            $this->entityManager->createEntity(ScheduledJob::ENTITY_TYPE, [
                'job' => $jobName,
                'status' => ScheduledJob::STATUS_ACTIVE,
                'scheduling' => $defs['scheduling'],
                'isInternal' => true,
                'name' => $name,
            ]);
        }

        $internalScheduledJobList = $this->entityManager
            ->getRDBRepository(ScheduledJob::ENTITY_TYPE)
            ->where([
                'isInternal' => true,
            ])
            ->find();

        foreach ($internalScheduledJobList as $scheduledJob) {
            $jobName = $scheduledJob->get('job');

            if (!in_array($jobName, $systemJobNameList)) {
                $this->entityManager
                    ->getRDBRepository(ScheduledJob::ENTITY_TYPE)
                    ->deleteFromDb($scheduledJob->getId());
            }
        }
    }
}
