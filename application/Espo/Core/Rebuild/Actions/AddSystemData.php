<?php


namespace Espo\Core\Rebuild\Actions;

use Espo\Core\Rebuild\RebuildAction;
use Espo\Entities\SystemData;
use Espo\ORM\EntityManager;

class AddSystemData implements RebuildAction
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    public function process(): void
    {
        $entity = $this->entityManager->getEntityById(SystemData::ENTITY_TYPE, SystemData::ONLY_ID);

        if ($entity) {
            return;
        }

        $this->entityManager->createEntity(SystemData::ENTITY_TYPE, ['id' => SystemData::ONLY_ID]);
    }
}
