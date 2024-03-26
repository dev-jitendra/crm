<?php


namespace Espo\Core\Select\Bool;

use Espo\Core\Select\OrmSelectBuilder;
use Espo\Core\Exceptions\Error;
use Espo\Core\Select\SelectManager;
use Espo\Core\Select\Bool\FilterFactory as BoolFilterFactory;

use Espo\ORM\Query\Select;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereClause;

use Espo\Entities\User;

class Applier
{
    public function __construct(
        private string $entityType,
        private User $user,
        private BoolFilterFactory $boolFilterFactory,
        private SelectManager $selectManager
    ) {}

    
    public function apply(QueryBuilder $queryBuilder, array $boolFilterNameList): void
    {
        $orGroupBuilder = new OrGroupBuilder();

        $isMultiple = count($boolFilterNameList) > 1;

        if ($isMultiple) {
            $queryBefore = $queryBuilder->build();
        }

        foreach ($boolFilterNameList as $filterName) {
            $this->applyBoolFilter($queryBuilder, $orGroupBuilder, $filterName);
        }

        if ($isMultiple) {
            $this->handleMultiple($queryBefore, $queryBuilder);
        }

        $queryBuilder->where(
            $orGroupBuilder->build()
        );
    }

    
    private function applyBoolFilter(
        QueryBuilder $queryBuilder,
        OrGroupBuilder $orGroupBuilder,
        string $filterName
    ): void {

        if ($this->boolFilterFactory->has($this->entityType, $filterName)) {
            $filter = $this->boolFilterFactory->create($this->entityType, $this->user, $filterName);

            $filter->apply($queryBuilder, $orGroupBuilder);

            return;
        }

        
        if (
            $this->selectManager->hasBoolFilter($filterName) &&
            $queryBuilder instanceof OrmSelectBuilder
        ) {
            $rawWhereClause = $this->selectManager->applyBoolFilterToQueryBuilder($queryBuilder, $filterName);

            $whereItem = WhereClause::fromRaw($rawWhereClause);

            $orGroupBuilder->add($whereItem);

            return;
        }

        throw new Error("No bool filter '{$filterName}' for '{$this->entityType}'.");
    }

    private function handleMultiple(Select $queryBefore, QueryBuilder $queryBuilder): void
    {
        $queryAfter = $queryBuilder->build();

        $joinCountBefore = count($queryBefore->getJoins()) + count($queryBefore->getLeftJoins());
        $joinCountAfter = count($queryAfter->getJoins()) + count($queryAfter->getLeftJoins());

        if ($joinCountBefore < $joinCountAfter) {
            $queryBuilder->distinct();
        }
    }
}
