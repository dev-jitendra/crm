<?php


namespace Espo\Core\Job\Preparator;

use Espo\Core\Job\Job\Status;
use Espo\Core\Utils\DateTime;
use Espo\Entities\Job;
use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use DateTimeImmutable;


class CollectionHelper
{
    public function __construct(private EntityManager $entityManager)
    {}

    
    public function prepare(Collection $collection, Data $data, DateTimeImmutable $executeTime): void
    {
        foreach ($collection as $entity) {
            $this->prepareItem($entity, $data, $executeTime);
        }
    }

    
    private function prepareItem(Entity $entity, Data $data, DateTimeImmutable $executeTime): void
    {
        $running = $this->entityManager
            ->getRDBRepository(Job::ENTITY_TYPE)
            ->select('id')
            ->where([
                'scheduledJobId' => $data->getId(),
                'status' => [
                    Status::RUNNING,
                    Status::READY,
                ],
                'targetType' => $entity->getEntityType(),
                'targetId' => $entity->getId(),
            ])
            ->findOne();

        if ($running) {
            return;
        }

        $countPending = $this->entityManager
            ->getRDBRepository(Job::ENTITY_TYPE)
            ->where([
                'scheduledJobId' => $data->getId(),
                'status' => Status::PENDING,
                'targetType' => $entity->getEntityType(),
                'targetId' => $entity->getId(),
            ])
            ->count();

        if ($countPending > 1) {
            return;
        }

        $job = $this->entityManager->getNewEntity(Job::ENTITY_TYPE);

        $job->set([
            'name' => $data->getName(),
            'scheduledJobId' => $data->getId(),
            'executeTime' => $executeTime->format(DateTime::SYSTEM_DATE_TIME_FORMAT),
            'targetType' => $entity->getEntityType(),
            'targetId' => $entity->getId(),
        ]);

        $this->entityManager->saveEntity($job);
    }
}
