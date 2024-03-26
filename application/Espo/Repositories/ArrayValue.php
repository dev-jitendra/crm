<?php


namespace Espo\Repositories;

use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Entities\ArrayValue as ArrayValueEntity;
use Espo\ORM\Entity;
use Espo\Core\Repositories\Database;

use RuntimeException;
use LogicException;


class ArrayValue extends Database
{
    protected $hooksDisabled = true;

    private const ITEM_MAX_LENGTH = 100;

    public function storeEntityAttribute(CoreEntity $entity, string $attribute, bool $populateMode = false): void
    {
        if ($entity->getAttributeType($attribute) !== Entity::JSON_ARRAY) {
            throw new LogicException("ArrayValue: Can't store non array attribute.");
        }

        if ($entity->getAttributeType('notStorable')) {
            return;
        }

        if (!$entity->getAttributeParam($attribute, 'storeArrayValues')) {
            return;
        }

        if (!$entity->has($attribute)) {
            return;
        }

        $valueList = $entity->get($attribute);

        if (is_null($valueList)) {
            $valueList = [];
        }

        if (!is_array($valueList)) {
            throw new RuntimeException("ArrayValue: Bad value passed to JSON_ARRAY attribute {$attribute}.");
        }

        $valueList = array_unique($valueList);
        $toSkipValueList = [];

        $isTransaction = false;

        if (!$entity->isNew() && !$populateMode) {
            $this->entityManager->getTransactionManager()->start();

            $isTransaction = true;

            $existingList = $this
                ->select(['id', 'value'])
                ->where([
                    'entityType' => $entity->getEntityType(),
                    'entityId' => $entity->getId(),
                    'attribute' => $attribute,
                ])
                ->forUpdate()
                ->find();

            foreach ($existingList as $existing) {
                if (!in_array($existing->get('value'), $valueList)) {
                    $this->deleteFromDb($existing->getId());

                    continue;
                }

                $toSkipValueList[] = $existing->get('value');
            }
        }

        $itemMaxLength = $this->entityManager
            ->getDefs()
            ->getEntity(ArrayValueEntity::ENTITY_TYPE)
            ->getField('value')
            ->getParam('maxLength') ?? self::ITEM_MAX_LENGTH;

        foreach ($valueList as $value) {
            if (in_array($value, $toSkipValueList)) {
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            if (strlen($value) > $itemMaxLength) {
                $value = substr($value, 0, $itemMaxLength);
            }

            $arrayValue = $this->getNew();

            $arrayValue->set([
                'entityType' => $entity->getEntityType(),
                'entityId' => $entity->getId(),
                'attribute' => $attribute,
                'value' => $value,
            ]);

            $this->save($arrayValue);
        }

        if ($isTransaction) {
            $this->entityManager->getTransactionManager()->commit();
        }
    }

    public function deleteEntityAttribute(CoreEntity $entity, string $attribute): void
    {
        if (!$entity->hasId()) {
            throw new LogicException("ArrayValue: Can't delete {$attribute} w/o id given.");
        }

        $this->entityManager->getTransactionManager()->start();

        $list = $this
            ->select(['id'])
            ->where([
                'entityType' => $entity->getEntityType(),
                'entityId' => $entity->getId(),
                'attribute' => $attribute,
            ])
            ->forUpdate()
            ->find();

        foreach ($list as $arrayValue) {
            $this->deleteFromDb($arrayValue->getId());
        }

        $this->entityManager->getTransactionManager()->commit();
    }
}
