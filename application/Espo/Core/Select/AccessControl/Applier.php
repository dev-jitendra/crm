<?php


namespace Espo\Core\Select\AccessControl;

use Espo\Core\Select\OrmSelectBuilder;
use Espo\Core\Select\AccessControl\FilterFactory as AccessControlFilterFactory;
use Espo\Core\Select\AccessControl\FilterResolverFactory as AccessControlFilterResolverFactory;
use Espo\Core\Select\SelectManager;

use Espo\Entities\User;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

use RuntimeException;

class Applier
{
    public function __construct(
        private string $entityType,
        private User $user,
        private AccessControlFilterFactory $accessControlFilterFactory,
        private AccessControlFilterResolverFactory $accessControlFilterResolverFactory,
        private SelectManager $selectManager
    ) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        
        if (
            $this->selectManager->hasInheritedAccessMethod() &&
            $queryBuilder instanceof OrmSelectBuilder
        ) {
            $this->selectManager->applyAccessToQueryBuilder($queryBuilder);

            return;
        }

        $this->applyMandatoryFilter($queryBuilder);

        $accessControlFilterResolver = $this->accessControlFilterResolverFactory
            ->create($this->entityType, $this->user);

        $filterName = $accessControlFilterResolver->resolve();

        if (!$filterName) {
            return;
        }

        
        if (
            $this->selectManager->hasInheritedAccessFilterMethod($filterName) &&
            $queryBuilder instanceof OrmSelectBuilder
        ) {
            $this->selectManager->applyAccessFilterToQueryBuilder($queryBuilder, $filterName);

            return;
        }

        if ($this->accessControlFilterFactory->has($this->entityType, $filterName)) {
            $filter = $this->accessControlFilterFactory
                ->create($this->entityType, $this->user, $filterName);

            $filter->apply($queryBuilder);

            return;
        }

        throw new RuntimeException("No access filter '{$filterName}' for '{$this->entityType}'.");
    }

    private function applyMandatoryFilter(QueryBuilder $queryBuilder): void
    {
        $filter = $this->accessControlFilterFactory
            ->create($this->entityType, $this->user, 'mandatory');

        $filter->apply($queryBuilder);
    }
}
