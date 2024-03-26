<?php


namespace Espo\Modules\Crm\Classes\FieldProcessing\TargetList;

use Espo\Modules\Crm\Entities\TargetList;
use Espo\ORM\Entity;

use Espo\Core\Utils\Metadata;

use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;


class EntryCountLoader implements Loader
{
    
    private array $targetLinkList;

    private EntityManager $entityManager;
    private Metadata $metadata;

    public function __construct(EntityManager $entityManager, Metadata $metadata)
    {
        $this->entityManager = $entityManager;
        $this->metadata = $metadata;

        $this->targetLinkList = $this->metadata->get(['scopes', 'TargetList', 'targetLinkList']) ?? [];
    }

    public function process(Entity $entity, Params $params): void
    {
        if (
            $params->hasSelect() &&
            !in_array('entryCount', $params->getSelect() ?? [])
        ) {
            return;
        }

        $count = 0;

        foreach ($this->targetLinkList as $link) {
            $count += $this->entityManager
                ->getRDBRepository(TargetList::ENTITY_TYPE)
                ->getRelation($entity, $link)
                ->count();
        }

        $entity->set('entryCount', $count);
    }
}
