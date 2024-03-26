<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Exceptions\Error;

use Espo\Core\Di;

class AttributeType extends \Espo\Core\Formula\Functions\AttributeType implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    public function process(\stdClass $item)
    {
        if (count($item->value) < 3) {
            throw new Error("record\\attribute: too few arguments.");
        }

        $entityType = $this->evaluate($item->value[0]);
        $id = $this->evaluate($item->value[1]);
        $attribute = $this->evaluate($item->value[2]);

        if (!$entityType) {
            throw new Error("Formula record\\attribute: Empty entityType.");
        }

        if (!$id) {
            return null;
        }

        if (!$attribute) {
            throw new Error("Formula record\\attribute: Empty attribute.");
        }

        $entity = $this->entityManager->getEntity($entityType, $id);

        if (!$entity) {
            return null;
        }

        return $this->attributeFetcher->fetch($entity, $attribute);
    }
}
