<?php


namespace Espo\Core\FieldProcessing\VersionNumber;

use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;

class BeforeSaveProcessor
{

    public function __construct(private Metadata $metadata)
    {}

    public function process(Entity $entity): void
    {
        $optimisticConcurrencyControl = $this->metadata
            ->get(['entityDefs', $entity->getEntityType(), 'optimisticConcurrencyControl']);

        if (!$optimisticConcurrencyControl) {
            return;
        }

        if ($entity->isNew()) {
            $entity->set('versionNumber', 1);

            return;
        }

        $entity->clear('versionNumber');

        if (!$entity->hasFetched('versionNumber')) {
            return;
        }

        $versionNumber = $entity->getFetched('versionNumber');

        if ($versionNumber === null) {
            $versionNumber = 0;
        }

        $versionNumber++;

        $entity->set('versionNumber', $versionNumber);
    }
}
