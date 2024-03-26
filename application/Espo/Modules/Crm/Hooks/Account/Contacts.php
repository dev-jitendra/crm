<?php


namespace Espo\Modules\Crm\Hooks\Account;

use Espo\ORM\{
    Entity,
    EntityManager,
};

class Contacts
{
    
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function afterRelate(Entity $entity, array $options = [], array $data = []): void
    {
        $relationName = $data['relationName'] ?? null;
        $foreignEntity = $data['foreignEntity'] ?? null;

        if ($relationName === 'contacts' && $foreignEntity) {
            if (!$foreignEntity->get('accountId') && $foreignEntity->has('accountId')) {
                $foreignEntity->set('accountId', $entity->getId());

                $this->entityManager->saveEntity($foreignEntity);
            }
        }
    }

    
    public function afterUnrelate(Entity $entity, array $options = [], array $data = []): void
    {
        $relationName = $data['relationName'] ?? null;
        $foreignEntity = $data['foreignEntity'] ?? null;

        if ($relationName === 'contacts' && $foreignEntity) {
            if ($foreignEntity->get('accountId') && $foreignEntity->get('accountId') === $entity->getId()) {
                $foreignEntity->set('accountId', null);

                $this->entityManager->saveEntity($foreignEntity);
            }
        }
    }
}
