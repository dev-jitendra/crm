<?php


namespace Espo\Hooks\LayoutSet;

use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

use Espo\Entities\LayoutSet;
use Espo\Entities\Team;
use Espo\Entities\Portal;

class Removal
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function afterRemove(Entity $entity): void
    {
        $updateQuery1 = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Team::ENTITY_TYPE)
            ->set([
                'layoutSetId' => null,
            ])
            ->where([
                'layoutSetId' => $entity->getId(),
            ])
            ->build();

        $this->entityManager
            ->getQueryExecutor()
            ->execute($updateQuery1);

        $updateQuery2 = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Portal::ENTITY_TYPE)
            ->set([
                'layoutSetId' => null,
            ])
            ->where([
                'layoutSetId' => $entity->getId(),
            ])
            ->build();

        $this->entityManager
            ->getQueryExecutor()
            ->execute($updateQuery2);
    }
}
