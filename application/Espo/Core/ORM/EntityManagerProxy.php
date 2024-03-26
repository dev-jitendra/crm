<?php


namespace Espo\Core\ORM;

use Espo\ORM\Entity;
use Espo\ORM\Metadata;
use Espo\ORM\Repository\RDBRepository;
use Espo\ORM\Repository\Repository;
use Espo\ORM\Executor\SqlExecutor;
use Espo\Core\Container;

class EntityManagerProxy
{
    private ?EntityManager $entityManager = null;

    public function __construct(private Container $container)
    {}

    private function getEntityManager(): EntityManager
    {
        if (!$this->entityManager) {
            $this->entityManager = $this->container->getByClass(EntityManager::class);
        }

        return $this->entityManager;
    }

    public function getNewEntity(string $entityType): Entity
    {
        return $this->getEntityManager()->getNewEntity($entityType);
    }

    public function getEntityById(string $entityType, string $id): ?Entity
    {
        return $this->getEntityManager()->getEntityById($entityType, $id);
    }

    public function getEntity(string $entityType, ?string $id = null): ?Entity
    {
        return $this->getEntityManager()->getEntity($entityType, $id);
    }

    
    public function saveEntity(Entity $entity, array $options = [])
    {
        
        
        return $this->getEntityManager()->saveEntity($entity, $options);
    }

    
    public function getRepository(string $entityType): Repository
    {
        return $this->getEntityManager()->getRepository($entityType);
    }

    
    public function getRDBRepository(string $entityType): RDBRepository
    {
        return $this->getEntityManager()->getRDBRepository($entityType);
    }

    public function getMetadata(): Metadata
    {
        return $this->getEntityManager()->getMetadata();
    }

    public function getSqlExecutor(): SqlExecutor
    {
        return $this->getEntityManager()->getSqlExecutor();
    }

    
    public function getRDBRepositoryByClass(string $className): RDBRepository
    {
        return $this->getEntityManager()->getRDBRepositoryByClass($className);
    }

    
    public function getRepositoryByClass(string $className): Repository
    {
        return $this->getEntityManager()->getRepositoryByClass($className);
    }
}
