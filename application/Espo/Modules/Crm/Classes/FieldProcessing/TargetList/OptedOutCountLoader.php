<?php


namespace Espo\Modules\Crm\Classes\FieldProcessing\TargetList;

use Espo\ORM\Entity;
use Espo\Modules\Crm\Entities\TargetList;
use Espo\Core\Utils\Metadata;
use Espo\Core\FieldProcessing\Loader;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;


class OptedOutCountLoader implements Loader
{
    
    private array $targetLinkList;

    public function __construct(private EntityManager $entityManager, private Metadata $metadata)
    {

        $this->targetLinkList = $this->metadata->get(['scopes', 'TargetList', 'targetLinkList']) ?? [];
    }

    public function process(Entity $entity, Params $params): void
    {
        if (
            $params->hasSelect() &&
            !in_array('optedOutCount', $params->getSelect() ?? [])
        ) {
            return;
        }

        assert($entity instanceof TargetList);

        $count = 0;

        foreach ($this->targetLinkList as $link) {
            $foreignEntityType = $entity->getRelationParam($link, 'entity');

            $count += $this->entityManager
                ->getRDBRepository($foreignEntityType)
                ->join('targetLists')
                ->where([
                    'targetListsMiddle.targetListId' => $entity->getId(),
                    'targetListsMiddle.optedOut' => true,
                ])
                ->count();
        }

        $entity->set('optedOutCount', $count);
    }
}
