<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\ORM\EntityManager;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class ExistsType extends BaseFunction implements
    Di\EntityManagerAware
{
    use Di\EntityManagerSetter;

    
    protected $entityManager;

    public function process(ArgumentList $args)
    {
        if (count($args) < 3) {
            $this->throwTooFewArguments(3);
        }

        $entityType = $this->evaluate($args[0]);

        $whereClause = [];

        $i = 1;
        while ($i < count($args) - 1) {
            $key = $this->evaluate($args[$i]);
            $value = $this->evaluate($args[$i + 1]);
            $whereClause[] = [$key => $value];
            $i = $i + 2;
        }

        return (bool) $this->entityManager
            ->getRDBRepository($entityType)
            ->where($whereClause)
            ->findOne();
    }
}
