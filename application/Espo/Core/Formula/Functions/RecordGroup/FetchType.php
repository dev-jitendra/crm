<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Func;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use stdClass;

class FetchType implements Func
{
    public function __construct(private EntityManager $entityManager) {}

    public function process(EvaluatedArgumentList $arguments): ?stdClass
    {
        if (count($arguments) < 2) {
            throw TooFewArguments::create(1);
        }

        $entityType = $arguments[0] ?? null;
        $id = $arguments[1] ?? null;

        if (!is_string($entityType)) {
            throw BadArgumentType::create(1, 'string');
        }

        if (!is_string($id)) {
            throw BadArgumentType::create(2, 'string');
        }

        $entity = $this->entityManager->getEntityById($entityType, $id);

        if (!$entity) {
            return null;
        }

        $this->load($entity);

        return $entity->getValueMap();
    }

    private function load(Entity $entity): void
    {
        if (!$entity instanceof CoreEntity) {
            return;
        }

        $fieldDefsList = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType())
            ->getFieldList();

        foreach ($fieldDefsList as $fieldDefs) {
            $field = $fieldDefs->getName();

            if ($fieldDefs->getType() === 'linkMultiple') {
                $entity->loadLinkMultipleField($field);
            }
        }
    }
}
