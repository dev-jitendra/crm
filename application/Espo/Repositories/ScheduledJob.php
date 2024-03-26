<?php


namespace Espo\Repositories;

use Espo\Entities\Job as JobEntity;
use Espo\ORM\Entity;
use Espo\Core\Job\Job\Status;
use Espo\Core\Repositories\Database;


class ScheduledJob extends Database
{
    protected $hooksDisabled = true;

    protected function afterSave(Entity $entity, array $options = [])
    {
        parent::afterSave($entity, $options);

        if ($entity->isAttributeChanged('scheduling')) {
            $jobList = $this->entityManager
                ->getRDBRepository(JobEntity::ENTITY_TYPE)
                ->where([
                    'scheduledJobId' => $entity->getId(),
                    'status' => Status::PENDING,
                ])
                ->find();

            foreach ($jobList as $job) {
                $this->entityManager->removeEntity($job);
            }
        }
    }
}
