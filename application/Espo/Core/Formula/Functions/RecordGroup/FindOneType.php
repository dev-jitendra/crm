<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error as FormulaError;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Di;

class FindOneType extends BaseFunction implements
    Di\EntityManagerAware,
    Di\SelectBuilderFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\SelectBuilderFactorySetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 3) {
            $this->throwTooFewArguments(3);
        }

        $entityType = $this->evaluate($args[0]);
        $orderBy = $this->evaluate($args[1]);
        $order = $this->evaluate($args[2]) ?? 'ASC';

        $builder = $this->selectBuilderFactory
            ->create()
            ->from($entityType);

        $whereClause = [];

        if (count($args) <= 4) {
            $filter = null;

            if (count($args) == 4) {
                $filter = $this->evaluate($args[3]);
            }

            if ($filter && !is_string($filter)) {
                $this->throwBadArgumentType(4, 'string');
            }

            if ($filter) {
                $builder->withPrimaryFilter($filter);
            }
        }
        else {
            $i = 3;

            while ($i < count($args) - 1) {
                $key = $this->evaluate($args[$i]);
                $value = $this->evaluate($args[$i + 1]);

                $whereClause[] = [$key => $value];

                $i = $i + 2;
            }
        }

        try {
            $queryBuilder = $builder->buildQueryBuilder();
        }
        catch (BadRequest|Error|Forbidden $e) {
            throw new FormulaError($e->getMessage(), $e->getCode(), $e);
        }

        if (!empty($whereClause)) {
            $queryBuilder->where($whereClause);
        }

        if ($orderBy) {
            $queryBuilder->order($orderBy, $order);
        }

        $queryBuilder->select(['id']);

        $entity = $this->entityManager
            ->getRDBRepository($entityType)
            ->clone($queryBuilder->build())
            ->findOne();

        if ($entity) {
            return $entity->getId();
        }

        return null;
    }
}
