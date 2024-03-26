<?php


namespace Espo\Core\Formula\Functions\EntityGroup;

use Espo\Core\Exceptions\Error;

use Espo\Core\Di;

use stdClass;
use PDO;

class SumRelatedType extends \Espo\Core\Formula\Functions\Base implements
    Di\EntityManagerAware,
    Di\SelectBuilderFactoryAware
{
    use Di\EntityManagerSetter;
    use Di\SelectBuilderFactorySetter;

    
    public function process(stdClass $item)
    {
        if (count($item->value) < 2) {
            throw new Error("sumRelated: Too few arguments.");
        }

        $link = $this->evaluate($item->value[0]);

        if (empty($link)) {
            throw new Error("No link passed to sumRelated function.");
        }

        $field = $this->evaluate($item->value[1]);

        if (empty($field)) {
            throw new Error("No field passed to sumRelated function.");
        }

        $filter = null;

        if (count($item->value) > 2) {
            $filter = $this->evaluate($item->value[2]);
        }

        $entity = $this->getEntity();

        $entityManager = $this->entityManager;

        $foreignEntityType = $entity->getRelationParam($link, 'entity');

        if (empty($foreignEntityType)) {
            throw new Error();
        }

        $foreignLink = $entity->getRelationParam($link, 'foreign');
        $foreignLinkAlias = $foreignLink . 'SumRelated';

        if (empty($foreignLink)) {
            throw new Error("No foreign link for link {$link}.");
        }

        $builder = $this->selectBuilderFactory
            ->create()
            ->from($foreignEntityType);

        if ($filter) {
            $builder->withPrimaryFilter($filter);
        }

        $queryBuilder = $builder->buildQueryBuilder();

        $queryBuilder->select([
            [$foreignLinkAlias . '.id', 'foreignId'],
            'SUM:' . $field,
        ]);

        if ($entity->getRelationType($link) === 'hasChildren') {
            $queryBuilder
                ->join(
                    $entity->getEntityType(),
                    $foreignLinkAlias,
                    [
                         $foreignLinkAlias . '.id:' => $foreignLink . 'Id',
                        'deleted' => false,
                        $foreignLinkAlias . '.id!=' => null,
                    ]
                )
                ->where([
                    $foreignLink . 'Type'  => $entity->getEntityType(),
                ]);
        }
        else {
            $queryBuilder->join($foreignLink, $foreignLinkAlias);
        }

        $queryBuilder->where([
            $foreignLinkAlias . '.id' => $entity->getId(),
        ]);

        if ($queryBuilder->build()->isDistinct()) {
            

            $sqQueryBuilder = clone $queryBuilder;

            $sqQueryBuilder
                ->order([])
                ->select(['id']);

            $queryBuilder->where([
                'id=s' => $sqQueryBuilder->build(),
            ]);
        }

        $queryBuilder->group($foreignLinkAlias . '.id');

        $sth = $entityManager->getQueryExecutor()->execute($queryBuilder->build());

        $rowList = $sth->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rowList)) {
            return 0.0;
        }

        $stringValue = $rowList[0]['SUM:' . $field];

        return floatval($stringValue);
    }
}
