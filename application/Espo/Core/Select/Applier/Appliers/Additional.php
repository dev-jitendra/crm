<?php


namespace Espo\Core\Select\Applier\Appliers;

use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\Core\Select\SearchParams;
use Espo\Core\InjectableFactory;
use Espo\Core\Select\Applier\AdditionalApplier;
use Espo\Core\Binding\BindingContainerBuilder;

use Espo\Entities\User;

class Additional
{
    public function __construct(private User $user, private InjectableFactory $injectableFactory)
    {}

    
    public function apply(array $classNameList, QueryBuilder $queryBuilder, SearchParams $searchParams): void
    {
        foreach ($classNameList as $className) {
            $applier = $this->createApplier($className);

            $applier->apply($queryBuilder, $searchParams);
        }
    }

    
    private function createApplier(string $className): AdditionalApplier
    {
        return $this->injectableFactory->createWithBinding(
            $className,
            BindingContainerBuilder::create()
                ->bindInstance(User::class, $this->user)
                ->build()
        );
    }
}
