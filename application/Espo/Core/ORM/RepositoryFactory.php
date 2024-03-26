<?php


namespace Espo\Core\ORM;

use Espo\Core\Binding\BindingContainerBuilder;
use Espo\Core\Binding\ContextualBinder;
use Espo\Core\InjectableFactory;
use Espo\ORM\Entity as Entity;
use Espo\ORM\EntityFactory as EntityFactoryInterface;
use Espo\ORM\Repository\Repository;
use Espo\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;

class RepositoryFactory implements RepositoryFactoryInterface
{
    public function __construct(
        private EntityFactoryInterface $entityFactory,
        private InjectableFactory $injectableFactory,
        private ClassNameProvider $classNameProvider
    ) {}

    public function create(string $entityType): Repository
    {
        $className = $this->getClassName($entityType);

        return $this->injectableFactory->createWithBinding(
            $className,
            BindingContainerBuilder::create()
                ->bindInstance(EntityFactoryInterface::class, $this->entityFactory)
                ->bindInstance(EntityFactory::class, $this->entityFactory)
                ->inContext(
                    $className,
                    function (ContextualBinder $binder) use ($entityType) {
                        $binder->bindValue('$entityType', $entityType);
                    }
                )
                ->build()
        );
    }

    
    private function getClassName(string $entityType): string
    {
        
        return $this->classNameProvider->getRepositoryClassName($entityType);
    }
}
