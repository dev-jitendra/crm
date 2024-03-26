<?php


namespace Espo\Core\FieldProcessing\LinkParent;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;


class TargetLoader implements LoaderInterface
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity, Params $params): void
    {
        $targetType = $entity->get('targetType');
        $targetId = $entity->get('targetId');

        if (!$targetType || !$targetId) {
            return;
        }

        if (!$this->entityManager->hasRepository($targetType)) {
            return;
        }

        $query = $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->from($targetType)
            ->withDeleted()
            ->where([
                'id' => $targetId,
            ])
            ->build();

        $target = $this->entityManager
            ->getRDBRepository($targetType)
            ->clone($query)
            ->findOne();

        if (!$target) {
            return;
        }

        if (!$target->get('name')) {
            return;
        }

        $entity->set('targetName', $target->get('name'));
    }
}
