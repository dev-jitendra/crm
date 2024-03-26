<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class CreateType extends BaseFunction implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        $args = $this->evaluate($args);

        $entityType = $args[0];

        if (!is_string($entityType)) {
            $this->throwBadArgumentType(1, 'string');
        }

        $data = [];

        $i = 1;

        while ($i < count($args) - 1) {
            $attribute = $args[$i];

            if (!is_string($entityType)) {
                $this->throwBadArgumentType($i + 1, 'string');
            }

            

            $value = $args[$i + 1];

            $data[$attribute] = $value;

            $i = $i + 2;
        }

        $entity = $this->entityManager->createEntity($entityType, $data);

        return $entity->getId();
    }
}
