<?php


namespace Espo\Core\Record;

use Espo\Core\Acl;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;


class EntityProvider
{
    public function __construct(
        private EntityManager $entityManager,
        private Acl $acl
    ) {}

    
    public function get(string $className, string $id): Entity
    {
        $entity = $this->entityManager
            ->getRDBRepositoryByClass($className)
            ->getById($id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntityRead($entity)) {
            throw new Forbidden();
        }

        return $entity;
    }
}
