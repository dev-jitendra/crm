<?php


namespace Espo\ORM;

use Espo\ORM\Value\ValueAccessorFactory;

interface EntityFactory
{
    
    public function create(string $entityType): Entity;

    
    public function setEntityManager(EntityManager $entityManager): void;

    
    public function setValueAccessorFactory(ValueAccessorFactory $valueAccessorFactory): void;
}
