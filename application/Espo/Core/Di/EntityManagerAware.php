<?php


namespace Espo\Core\Di;

use Espo\ORM\EntityManager;

interface EntityManagerAware
{
    public function setEntityManager(EntityManager $entityManager): void;
}
