<?php


namespace Espo\Hooks\GroupEmailFolder;

use Espo\Entities\GroupEmailFolder;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

class Order
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function beforeSave(Entity $entity): void
    {
        $order = $entity->getOrder();

        if ($order !== null) {
            return;
        }

        $order = $this->entityManager
            ->getRDBRepositoryByClass(GroupEmailFolder::class)
            ->max('order');

        if (!$order) {
            $order = 0;
        }

        $order++;

        $entity->set('order', $order);
    }
}
