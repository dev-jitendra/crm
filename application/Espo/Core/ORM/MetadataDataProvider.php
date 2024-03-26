<?php


namespace Espo\Core\ORM;

use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\Metadata\OrmMetadataData;
use Espo\ORM\MetadataDataProvider as MetadataDataProviderInterface;

class MetadataDataProvider implements MetadataDataProviderInterface
{
    public function __construct(
        private OrmMetadataData $ormMetadataData,
        private Metadata $metadata
    ) {}

    public function get(): array
    {
        $data = $this->ormMetadataData->getData();

        foreach (array_keys($data) as $entityType) {
            $data[$entityType]['fields'] = $this->metadata->get(['entityDefs', $entityType, 'fields']) ?? [];
        }

        return $data;
    }
}
