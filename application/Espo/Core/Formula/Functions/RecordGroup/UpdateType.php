<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class UpdateType extends BaseFunction implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments(2);
        }

        $args = $this->evaluate($args);

        $entityType = $args[0];
        $id = $args[1];

        if (!is_string($entityType)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!is_string($id)) {
            $this->throwBadArgumentType(2, 'string');
        }

        $data = [];

        $i = 2;

        while ($i < count($args) - 1) {
            $attribute = $args[$i];

            if (!is_string($entityType)) {
                $this->throwBadArgumentType($i + 1, 'string');
            }

            

            $value = $args[$i + 1];

            $data[$attribute] = $value;

            $i = $i + 2;
        }

        $em = $this->entityManager;

        $entity = $em->getEntity($entityType, $id);

        if (!$entity) {
            return false;
        }

        $entity->set($data);
        $em->saveEntity($entity);

        return true;
    }
}
