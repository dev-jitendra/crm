<?php


namespace Espo\Modules\Crm\Tools\MassEmail;

use Espo\Core\Utils\Metadata;
use Espo\ORM\Defs;
use Espo\Modules\Crm\Entities\TargetList;
use RuntimeException;

class Util
{
    
    private array $targetLinkList;

    public function __construct(
        private Defs $ormDefs,
        private Metadata $metadata
    ) {
        $this->targetLinkList = $this->metadata->get(['scopes', 'TargetList', 'targetLinkList']) ?? [];
    }

    public function getLinkByEntityType(string $entityType): string
    {
        foreach ($this->targetLinkList as $link) {
            $itemEntityType = $this->ormDefs
                ->getEntity(TargetList::ENTITY_TYPE)
                ->getRelation($link)
                ->getForeignEntityType();

            if ($itemEntityType === $entityType) {
                return $link;
            }
        }

        throw new RuntimeException("No link for $entityType.");
    }
}
