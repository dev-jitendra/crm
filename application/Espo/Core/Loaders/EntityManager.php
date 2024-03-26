<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\ORM\EntityManager as EntityManagerService;
use Espo\Core\ORM\EntityManagerFactory;

class EntityManager implements Loader
{
    private EntityManagerFactory $entityManagerFactory;

    public function __construct(EntityManagerFactory $entityManagerFactory)
    {
        $this->entityManagerFactory = $entityManagerFactory;
    }

    public function load(): EntityManagerService
    {
        return $this->entityManagerFactory->create();
    }
}
