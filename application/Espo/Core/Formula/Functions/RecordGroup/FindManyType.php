<?php


namespace Espo\Core\Formula\Functions\RecordGroup;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\Error as FormulaError;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Func;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Order;

class FindManyType implements Func
{
    public function __construct(
        private EntityManager $entityManager,
        private SelectBuilderFactory $selectBuilderFactory
    ) {}

    
    public function process(EvaluatedArgumentList $arguments): array
    {
        if (count($arguments) < 4) {
            throw TooFewArguments::create(4);
        }

        $entityType = $arguments[0];
        $limit = $arguments[1];
        $orderBy = $arguments[2];
        $order = $arguments[3] ?? Order::ASC;

        if (!is_string($entityType)) {
            throw BadArgumentType::create(1, 'string');
        }

        if (!is_int($limit)) {
            throw BadArgumentType::create(2, 'int');
        }

        if ($orderBy !== null && !is_string($orderBy)) {
            throw BadArgumentType::create(3, 'string|null');
        }

        if (!is_bool($order) && !is_string($orderBy)) {
            throw BadArgumentType::create(4, 'string|bool');
        }

        $builder = $this->selectBuilderFactory
            ->create()
            ->from($entityType);

        $whereClause = [];

        if (count($arguments) <= 5) {
            $filter = null;

            if (count($arguments) === 5) {
                $filter = $arguments[4];
            }

            if ($filter && !is_string($filter)) {
                throw BadArgumentType::create(5, 'string');
            }

            if ($filter) {
                $builder->withPrimaryFilter($filter);
            }
        }
        else {
            $i = 4;

            while ($i < count($arguments) - 1) {
                $key = $arguments[$i];
                $value = $arguments[$i + 1];

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

        $queryBuilder
            ->select(['id'])
            ->limit(0, $limit);

        $collection = $this->entityManager
            ->getRDBRepository($entityType)
            ->clone($queryBuilder->build())
            ->find();

        return array_map(
            fn (Entity $entity) => $entity->getId(),
            iterator_to_array($collection)
        );
    }
}
