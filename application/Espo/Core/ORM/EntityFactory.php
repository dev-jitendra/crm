<?php


namespace Espo\Core\ORM;

use Espo\Core\Binding\Binder;
use Espo\Core\Binding\BindingContainer;
use Espo\Core\Binding\BindingData;
use Espo\Core\InjectableFactory;
use Espo\ORM\Entity;
use Espo\ORM\EntityFactory as EntityFactoryInterface;
use Espo\ORM\EntityManager;
use Espo\ORM\Value\ValueAccessorFactory;

use RuntimeException;

class EntityFactory implements EntityFactoryInterface
{
    private ?EntityManager $entityManager = null;
    private ?ValueAccessorFactory $valueAccessorFactory = null;

    public function __construct(
        private ClassNameProvider $classNameProvider,
        private Helper $helper,
        private InjectableFactory $injectableFactory
    ) {}

    public function setEntityManager(EntityManager $entityManager): void
    {
        if ($this->entityManager) {
            throw new RuntimeException("EntityManager can be set only once.");
        }

        $this->entityManager = $entityManager;
    }

    public function setValueAccessorFactory(ValueAccessorFactory $valueAccessorFactory): void
    {
        if ($this->valueAccessorFactory) {
            throw new RuntimeException("ValueAccessorFactory can be set only once.");
        }

        $this->valueAccessorFactory = $valueAccessorFactory;
    }

    public function create(string $entityType): Entity
    {
        $className = $this->getClassName($entityType);

        if (!$this->entityManager) {
            throw new RuntimeException();
        }

        $defs = $this->entityManager->getMetadata()->get($entityType);

        if (is_null($defs)) {
            throw new RuntimeException("Entity '$entityType' is not defined in metadata.");
        }

        $bindingContainer = $this->getBindingContainer($className, $entityType, $defs);

        return $this->injectableFactory->createWithBinding($className, $bindingContainer);
    }

    
    private function getClassName(string $entityType): string
    {
        
        return $this->classNameProvider->getEntityClassName($entityType);
    }

    
    private function getBindingContainer(string $className, string $entityType, array $defs): BindingContainer
    {
        if (!$this->entityManager || !$this->valueAccessorFactory) {
            throw new RuntimeException();
        }

        $data = new BindingData();
        $binder = new Binder($data);

        $binder
            ->for($className)
            ->bindValue('$entityType', $entityType)
            ->bindValue('$defs', $defs)
            ->bindInstance(EntityManager::class, $this->entityManager)
            ->bindInstance(ValueAccessorFactory::class, $this->valueAccessorFactory)
            ->bindInstance(Helper::class, $this->helper);

        return new BindingContainer($data);
    }
}
