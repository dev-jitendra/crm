<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class UpdateRelationColumnType extends BaseFunction implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 6) {
            $this->throwTooFewArguments(6);
        }

        $entityType = $args[0];
        $id = $args[1];
        $link = $args[2];
        $foreignId = $args[3];
        $column = $args[4];
        $value = $args[5];

        if (!$entityType) {
            $this->throwError("Empty entityType.");
        }

        if (!$id) {
            return null;
        }

        if (!$link) {
            $this->throwError("Empty link.");
        }

        if (!$foreignId) {
            return null;
        }

        if (!$column) {
            $this->throwError("Empty column.");
        }

        if (!is_string($column)) {
            $this->throwError("Column is not string.");
        }

        $em = $this->entityManager;

        if (!$em->hasRepository($entityType)) {
            $this->throwError("Repository does not exist.");
        }

        $entity = $em->getEntity($entityType, $id);

        if (!$entity) {
            return null;
        }

        $em->getRDBRepository($entityType)
            ->getRelation($entity, $link)
            ->updateColumnsById($foreignId, [$column => $value]);

        return true;
    }
}
