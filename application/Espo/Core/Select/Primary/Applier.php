<?php


namespace Espo\Core\Select\Primary;

use Espo\Core\Exceptions\Error;
use Espo\Core\Select\SelectManager;
use Espo\Core\Select\OrmSelectBuilder;

use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\Entities\User;

class Applier
{
    public function __construct(
        private string $entityType,
        private User $user,
        private FilterFactory $primaryFilterFactory,
        private SelectManager $selectManager
    ) {}

    
    public function apply(QueryBuilder $queryBuilder, string $filterName): void
    {
        if ($this->primaryFilterFactory->has($this->entityType, $filterName)) {
            $filter = $this->primaryFilterFactory->create($this->entityType, $this->user, $filterName);

            $filter->apply($queryBuilder);

            return;
        }

        
        if (
            $this->selectManager->hasPrimaryFilter($filterName) &&
            $queryBuilder instanceof OrmSelectBuilder
        ) {
            $this->selectManager->applyPrimaryFilterToQueryBuilder($queryBuilder, $filterName);

            return;
        }

        throw new Error("No primary filter '{$filterName}' for '{$this->entityType}'.");
    }
}
