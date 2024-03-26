<?php


namespace Espo\Core\Job\Preparator\Preparators;

use Espo\Core\Utils\DateTime;
use Espo\Core\Job\Job\Status;
use Espo\Core\Job\Preparator;
use Espo\Core\Job\Preparator\Data;

use Espo\ORM\EntityManager;

use Espo\Entities\Job as JobEntity;

use DateTimeImmutable;

class ProcessJobGroupPreparator implements Preparator
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function prepare(Data $data, DateTimeImmutable $executeTime): void
    {
        $groupList = [];

        $query = $this->entityManager
            ->getQueryBuilder()
            ->select('group')
            ->from(JobEntity::ENTITY_TYPE)
            ->where([
                'status' => Status::PENDING,
                'queue' => null,
                'group!=' => null,
                'executeTime<=' => $executeTime->format(DateTime::SYSTEM_DATE_TIME_FORMAT),
            ])
            ->group('group')
            ->build();

        $sth = $this->entityManager->getQueryExecutor()->execute($query);

        while ($row = $sth->fetch()) {
            $group = $row['group'];

            if ($group === null) {
                continue;
            }

            $groupList[] = $group;
        }

        if (!count($groupList)) {
            return;
        }

        foreach ($groupList as $group) {
            $existingJob = $this->entityManager
                ->getRDBRepository(JobEntity::ENTITY_TYPE)
                ->select('id')
                ->where([
                    'scheduledJobId' => $data->getId(),
                    'targetGroup' => $group,
                    'status' => [
                        Status::RUNNING,
                        Status::READY,
                        Status::PENDING,
                    ],
                ])
                ->findOne();

            if ($existingJob) {
                continue;
            }

            $name = $data->getName() . ' :: ' . $group;

            $this->entityManager->createEntity(JobEntity::ENTITY_TYPE, [
                'scheduledJobId' => $data->getId(),
                'executeTime' => $executeTime->format(DateTime::SYSTEM_DATE_TIME_FORMAT),
                'name' => $name,
                'data' => [
                    'group' => $group,
                ],
                'targetGroup' => $group,
            ]);
        }
    }
}
