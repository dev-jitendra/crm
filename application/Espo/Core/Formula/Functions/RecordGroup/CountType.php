<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class CountType extends BaseFunction implements
    Di\EntityManagerAware,
    Di\SelectBuilderFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\SelectBuilderFactorySetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        $entityType = $this->evaluate($args[0]);

        if (count($args) < 3) {
            $filter = null;

            if (count($args) == 2) {
                $filter = $this->evaluate($args[1]);
            }

            $builder = $this->selectBuilderFactory
                ->create()
                ->from($entityType);

            if ($filter && !is_string($filter)) {
                $this->throwBadArgumentType(2, 'string');
            }

            if ($filter) {
                $builder->withPrimaryFilter($filter);
            }

            return $this->entityManager
                ->getRDBRepository($entityType)
                ->clone($builder->build())
                ->count();
        }

        $whereClause = [];

        $i = 1;

        while ($i < count($args) - 1) {
            $key = $this->evaluate($args[$i]);
            $value = $this->evaluate($args[$i + 1]);

            $whereClause[] = [$key => $value];

            $i = $i + 2;
        }

        return $this->entityManager
            ->getRDBRepository($entityType)
            ->where($whereClause)
            ->count();
    }
}
