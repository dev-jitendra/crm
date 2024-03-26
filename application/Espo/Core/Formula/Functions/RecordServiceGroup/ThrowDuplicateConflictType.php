<?php


namespace Espo\Core\Formula\Functions\RecordServiceGroup;

use Espo\Core\Di\EntityManagerAware;
use Espo\Core\Di\EntityManagerSetter;
use Espo\Core\Di\RecordServiceContainerAware;
use Espo\Core\Di\RecordServiceContainerSetter;
use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\ConflictSilent;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Utils\Json;

class ThrowDuplicateConflictType extends BaseFunction implements
    EntityManagerAware,
    RecordServiceContainerAware
{
    use EntityManagerSetter;
    use RecordServiceContainerSetter;

    
    public function process(ArgumentList $args)
    {
        if (empty($this->getVariables()->__isRecordService)) {
            $this->throwError("Can be called only from API script.");
        }

        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        $ids = $this->evaluate($args[0]);

        if (is_string($ids)) {
            $ids = [$ids];
        }

        if (!is_array($ids)) {
            $this->throwBadArgumentType(1);
        }

        $entityType = $this->getEntity()->getEntityType();

        $list = [];

        foreach ($ids as $id) {
            $entity = $this->entityManager->getEntityById($entityType, $id);

            if ($entity) {
                $this->recordServiceContainer->get($entityType)->prepareEntityForOutput($entity);
            }

            if (!$entity) {
                $entity = $this->entityManager->getNewEntity($entityType);
                $entity->set('name', $id);
            }

            $list[] = $entity->getValueMap();
        }

        throw ConflictSilent::createWithBody('duplicate', Json::encode($list));
    }
}
