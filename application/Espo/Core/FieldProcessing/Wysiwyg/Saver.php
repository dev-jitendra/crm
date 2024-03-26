<?php


namespace Espo\Core\FieldProcessing\Wysiwyg;

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
        if (!$entity->has($name)) {
            return;
        }

        if (!$entity->isAttributeChanged($name)) {
            return;
        }

        $contents = $entity->get($name);

        if (!$contents) {
            return;
        }

        $matches = [];

        $matchResult = preg_match_all("/\?entryPoint=attachment&amp;id=([^&=\"']+)/", $contents, $matches);

        if (!$matchResult) {
            return;
        }

        if (empty($matches[1]) || !is_array($matches[1])) {
            return;
        }

        foreach ($matches[1] as $id) {
            $attachment = $this->entityManager->getEntity('Attachment', $id);

            if (!$attachment) {
                continue;
            }

            if ($attachment->get('relatedId')) {
                continue;
            }

            $attachment->set([
                'relatedId' => $entity->getId(),
                'relatedType' => $entity->getEntityType(),
            ]);

            $this->entityManager->saveEntity($attachment);
        }
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

        foreach ($entityDefs->getFieldNameList() as $name) {
            $defs = $entityDefs->getField($name);

            if ($defs->getType() !== 'wysiwyg') {
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListMapCache[$entityType] = $list;

        return $list;
    }
}
