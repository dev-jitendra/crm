<?php


namespace Espo\Core\FieldProcessing\NextNumber;

use Espo\Core\Exceptions\Error;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Entities\NextNumber;

use Espo\Core\ORM\Entity;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Metadata;

use const STR_PAD_LEFT;

class BeforeSaveProcessor
{
    private Metadata $metadata;
    private EntityManager $entityManager;

    
    private $fieldListMapCache = [];

    public function __construct(Metadata $metadata, EntityManager $entityManager)
    {
        $this->metadata = $metadata;
        $this->entityManager = $entityManager;
    }

    
    public function processPopulate(Entity $entity, string $field): void
    {
        $fieldList = $this->getFieldList($entity->getEntityType());

        if (!in_array($field, $fieldList)) {
            throw new Error("Bad field.");
        }

        $this->processItem($entity, $field, [], true);
    }

    
    public function process(Entity $entity, array $options): void
    {
        $fieldList = $this->getFieldList($entity->getEntityType());

        foreach ($fieldList as $field) {
            $this->processItem($entity, $field, $options);
        }
    }

    
    private function processItem(Entity $entity, string $field, array $options, bool $populate = false): void
    {
        if (!empty($options[SaveOption::IMPORT])) {
            if ($entity->has($field)) {
                return;
            }
        }

        if (!$entity->isNew()) {
            if ($entity->isAttributeChanged($field)) {
                $entity->set($field, $entity->getFetched($field));
            }

            if (!$populate) {
                return;
            }
        }

        $this->entityManager->getTransactionManager()->start();

        $nextNumber = $this->entityManager
            ->getRDBRepository(NextNumber::ENTITY_TYPE)
            ->where([
                'fieldName' => $field,
                'entityType' => $entity->getEntityType(),
            ])
            ->forUpdate()
            ->findOne();

        if (!$nextNumber) {
            $nextNumber = $this->entityManager->getNewEntity(NextNumber::ENTITY_TYPE);

            $nextNumber->set('entityType', $entity->getEntityType());
            $nextNumber->set('fieldName', $field);
        }

        $entity->set($field, $this->composeNumberAttribute($nextNumber));

        $value = $nextNumber->get('value');

        if (!$value) {
            $value = 1;
        }

        $value++;

        $nextNumber->set('value', $value);

        $this->entityManager->saveEntity($nextNumber);

        $this->entityManager->getTransactionManager()->commit();
    }

    private function composeNumberAttribute(NextNumber $nextNumber): string
    {
        $entityType = $nextNumber->get('entityType');
        $fieldName = $nextNumber->get('fieldName');
        $value = $nextNumber->get('value');

        $prefix = $this->metadata->get(['entityDefs', $entityType, 'fields', $fieldName, 'prefix'], '');
        $padLength = $this->metadata->get(['entityDefs', $entityType, 'fields', $fieldName, 'padLength'], 0);

        return $prefix . str_pad(strval($value), $padLength, '0', STR_PAD_LEFT);
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

            if ($defs->getType() !== 'number') {
                continue;
            }

            $list[] = $name;
        }

        $this->fieldListMapCache[$entityType] = $list;

        return $list;
    }
}
