<?php


namespace Espo\ORM\Repository;

use Espo\ORM\Entity;

interface RepositoryFactory
{
    
    public function create(string $entityType): Repository;
}
