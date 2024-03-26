<?php


namespace Espo\Core\Di;

use Espo\ORM\EntityManager;

trait EntityManagerSetter
{
    
    protected $entityManager;

    public function setEntityManager(EntityManager $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
