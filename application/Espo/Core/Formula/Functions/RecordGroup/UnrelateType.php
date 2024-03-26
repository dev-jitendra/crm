<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

use Espo\Core\Di;

class UnrelateType extends BaseFunction implements
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

        $em = $this->entityManager;

        if (!$em->hasRepository($entityType)) {
            $this->throwError("Repository does not exist.");
        }

        $entity = $em->getEntityById($entityType, $id);

        if (!$entity) {
            return null;
        }

        $em->getRDBRepository($entityType)
            ->getRelation($entity, $link)
            ->unrelateById($foreignId);

        return true;
    }
}
