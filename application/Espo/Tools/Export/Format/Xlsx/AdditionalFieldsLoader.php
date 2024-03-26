<?php


namespace Espo\Tools\Export\Format\Xlsx;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\Tools\Export\AdditionalFieldsLoader as AdditionalFieldsLoaderInterface;


class AdditionalFieldsLoader implements AdditionalFieldsLoaderInterface
{
    public function __construct(private Metadata $metadata)
    {}

    public function load(Entity $entity, array $fieldList): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        foreach ($entity->getRelationList() as $link) {
            if (!in_array($link, $fieldList)) {
                continue;
            }

            if ($entity->getRelationType($link) === Entity::BELONGS_TO_PARENT) {
                if (!$entity->get($link . 'Name')) {
                    $entity->loadParentNameField($link);
                }
            }
            else if (
                (
                    (
                        $entity->getRelationType($link) === Entity::BELONGS_TO &&
                        $entity->getRelationParam($link, 'noJoin')
                    ) ||
                    $entity->getRelationType($link) === Entity::HAS_ONE
                ) &&
                $entity->hasAttribute($link . 'Name')
            ) {
                if (!$entity->get($link . 'Name') || !$entity->get($link . 'Id')) {
                    $entity->loadLinkField($link);
                }
            }
        }

        foreach ($fieldList as $field) {
            $fieldType = $this->metadata
                ->get(['entityDefs', $entity->getEntityType(), 'fields', $field, 'type']);

            if ($fieldType === 'linkMultiple' || $fieldType === 'attachmentMultiple') {
                if (!$entity->has($field . 'Ids') && $entity->hasLinkMultipleField($field)) {
                    $entity->loadLinkMultipleField($field);
                }
            }
        }
    }
}
