<?php


namespace Espo\Tools\Kanban;

use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Id\RecordIdGenerator;
use Espo\Core\Utils\Metadata;

class Orderer
{
    public function __construct(
        private EntityManager $entityManager,
        private Metadata $metadata,
        private RecordIdGenerator $idGenerator
    ) {}

    public function setEntityType(string $entityType): OrdererProcessor
    {
        return $this->createProcessor()->setEntityType($entityType);
    }

    public function setGroup(string $group): OrdererProcessor
    {
        return $this->createProcessor()->setGroup($group);
    }

    public function setUserId(string $userId): OrdererProcessor
    {
        return $this->createProcessor()->setUserId($userId);
    }

    public function setMaxNumber(?int $maxNumber): OrdererProcessor
    {
        return $this->createProcessor()->setMaxNumber($maxNumber);
    }

    public function createProcessor(): OrdererProcessor
    {
        return new OrdererProcessor(
            $this->entityManager,
            $this->metadata,
            $this->idGenerator
        );
    }
}
