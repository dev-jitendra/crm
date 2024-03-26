<?php


namespace Espo\Tools\UserSecurity\Password\Jobs;

use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Entities\PasswordChangeRequest;
use Espo\ORM\EntityManager;
use RuntimeException;

class RemoveRecoveryRequest implements Job
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function run(Data $data): void
    {
        $id = $data->get('id');

        if (!$id) {
            throw new RuntimeException();
        }

        $entity = $this->entityManager->getEntity(PasswordChangeRequest::ENTITY_TYPE, $id);

        if (!$entity) {
            return;
        }

        $this->entityManager->removeEntity($entity);
    }
}
