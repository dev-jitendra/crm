<?php


namespace Espo\Tools\Export\Format\Csv;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\Tools\Export\AdditionalFieldsLoader as AdditionalFieldsLoaderInterface;


class AdditionalFieldsLoader implements AdditionalFieldsLoaderInterface
{
    public function __construct(private Metadata $metadata) {}

    public function load(Entity $entity, array $fieldList): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        foreach ($fieldList as $field) {
            $fieldType = $this->metadata
                ->get(['entityDefs', $entity->getEntityType(), 'fields', $field, 'type']);

            if (
                $fieldType === 'linkMultiple' ||
                $fieldType === 'attachmentMultiple'
            ) {
                if (!$entity->has($field . 'Ids') && $entity->hasLinkMultipleField($field)) {
                    $entity->loadLinkMultipleField($field);
                }
            }
        }
    }
}
