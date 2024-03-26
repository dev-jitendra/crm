<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

use Espo\Core\Di;

class RelateType extends BaseFunction implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 4) {
            $this->throwTooFewArguments(4);
        }

        $entityType = $this->evaluate($args[0]);
        $id = $this->evaluate($args[1]);
        $link = $this->evaluate($args[2]);
        $foreignId = $this->evaluate($args[3]);

        if (!$entityType || !is_string($entityType)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!$id) {
            return null;
        }

        if (!$link || !is_string($link)) {
            $this->throwBadArgumentType(3, 'string');
        }

        if (!$foreignId) {
            return null;
        }

        $em = $this->entityManager;

        if (!$em->hasRepository($entityType)) {
            $this->throwError("Repository does not exist.");
        }

        $entity = $em->getEntityById($entityType, $id);

        if (!$entity) {
            return null;
        }

        $relation = $em->getRDBRepository($entityType)->getRelation($entity, $link);

        if (is_array($foreignId)) {
            foreach ($foreignId as $itemId) {
                $relation->relateById($itemId);
            }

            return true;
        }

        if (!is_string($foreignId)) {
            $this->throwError("foreignId type is wrong.");
        }

        $relation->relateById($foreignId);

        return true;
    }
}
