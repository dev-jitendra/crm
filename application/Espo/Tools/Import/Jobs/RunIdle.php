<?php


namespace Espo\Tools\Import\Jobs;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Core\Exceptions\Error;
use Espo\Tools\Import\ImportFactory;
use Espo\Tools\Import\Params as ImportParams;
use Espo\ORM\EntityManager;
use Espo\Entities\User;

class RunIdle implements Job
{
    public function __construct(
        private ImportFactory $factory,
        private EntityManager $entityManager
    ) {}

    
    public function run(Data $data): void
    {
        $raw = $data->getRaw();

        $entityType = $raw->entityType;
        $attachmentId = $raw->attachmentId;
        $importId = $raw->importId;
        $importAttributeList = $raw->importAttributeList;
        $userId = $raw->userId;

        $params = ImportParams::fromRaw($raw->params);

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        if (!$user) {
            throw new Error("Import: User not found.");
        }

        if (!$user->isActive()) {
            throw new Error("Import: User is not active.");
        }

        $this->factory
            ->create()
            ->setEntityType($entityType)
            ->setAttributeList($importAttributeList)
            ->setAttachmentId($attachmentId)
            ->setParams($params)
            ->setId($importId)
            ->setUser($user)
            ->run();
    }
}
