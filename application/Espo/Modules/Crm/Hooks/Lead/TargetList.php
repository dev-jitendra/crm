<?php


namespace Espo\Modules\Crm\Hooks\Lead;

use Espo\Core\Hook\Hook\AfterSave;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Repository\Option\SaveOptions;


class TargetList implements AfterSave
{
    public function __construct(private EntityManager $entityManager) {}

    public function afterSave(Entity $entity, SaveOptions $options): void
    {
        if (!$options->get(SaveOption::IMPORT)) {
            return;
        }

        $targetListId = $entity->get('targetListId');

        if (!$targetListId) {
            return;
        }

        $this->entityManager
            ->getRDBRepositoryByClass(Lead::class)
            ->getRelation($entity, 'targetLists')
            ->relateById($targetListId);
    }
}
