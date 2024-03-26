<?php


namespace Espo\Core\FieldProcessing\File;

use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Saver as SaverInterface;
use Espo\Core\FieldProcessing\Saver\Params;
use Espo\Core\ORM\EntityManager;


class Saver implements SaverInterface
{
    private EntityManager $entityManager;

    
    private $fieldListMapCache = [];

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(Entity $entity, Params $params): void
    {
        foreach ($this->getFieldList($entity->getEntityType()) as $name) {
            $this->processItem($entity, $name);
        }
    }

    private function processItem(Entity $entity, string $name): void
    {
        $attribute = $name . 'Id';

        if (!$entity->get($attribute)) {
            return;
        }

        if (!$entity->isAttributeChanged($attribute)) {
            return;
        }

        $attachment = $this->entityManager->getEntity('Attachment', $entity->get($attribute));

        if (!$attachment) {
            return;
        }

        $attachment->set([
            'relatedId' => $entity->getId(),
            'relatedType' => $entity->getEntityType(),
        ]);

        $this->entityManager->saveEntity($attachment);

        if ($entity->isNew()) {
            return;
        }

        $previousAttachmentId = $entity->getFetched($attribute);

        if (!$previousAttachmentId) {
            return;
        }

        $previousAttachment = $this->entityManager->getEntity('Attachment', $previousAttachmentId);

        if (!$previousAttachment) {
            return;
        }

        $this->entityManager->removeEntity($previousAttachment);
    }

    
    private function getFieldList(string $entityType): array
    {
        if (array_key_exists($entityType, $this->fieldListMapCache)) {
            return $this->fieldListMapCache[$entityType];
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entityType);

        $list = [];

        foreach ($entityDefs->getRelationNameList() as $name) {
            $defs = $entityDefs->getRelation($name);

            $type = $defs->getType();

            if (!$defs->hasForeignEntityType()) {
                continue;
            }

            $foreignEntityType = $defs->getForeignEntityType();

            if ($type !== Entity::BELONGS_TO || $foreignEntityType !== 'Attachment') {
                continue;
            }


            if (!$entityDefs->hasAttribute($name . 'Id')) {
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListMapCache[$entityType] = $list;

        return $list;
    }
}
