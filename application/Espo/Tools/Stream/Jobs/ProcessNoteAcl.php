<?php


namespace Espo\Tools\Stream\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;

use Espo\ORM\EntityManager;

use Espo\Tools\Stream\Service as Service;

class ProcessNoteAcl implements Job
{
    private Service $service;
    private EntityManager $entityManager;

    public function __construct(
        Service $service,
        EntityManager $entityManager
    ) {
        $this->service = $service;
        $this->entityManager = $entityManager;
    }

    public function run(Data $data): void
    {
        $targetType = $data->getTargetType();
        $targetId = $data->getTargetId();

        if (!$targetType || !$targetId) {
            return;
        }

        if (!$this->entityManager->hasRepository($targetType)) {
            return;
        }

        $entity = $this->entityManager->getEntityById($targetType, $targetId);

        if (!$entity) {
            return;
        }

        $this->service->processNoteAcl($entity, true);
    }
}
