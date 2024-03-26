<?php


namespace Espo\Modules\Crm\Hooks\Contact;

use Espo\ORM\EntityManager;
use Espo\ORM\Entity;

class Opportunities
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function afterRelate(Entity $entity, array $options = [], array $data = []): void
    {
        $relationName = $data['relationName'] ?? null;
        
        $foreignEntity = $data['foreignEntity'] ?? null;

        if ($relationName === 'opportunities' && $foreignEntity) {
            if (!$foreignEntity->get('contactId') && $foreignEntity->has('contactId')) {
                $foreignEntity->set('contactId', $entity->getId());

                $this->entityManager->saveEntity($foreignEntity);
            }
        }
    }

    
    public function afterUnrelate(Entity $entity, array $options = [], array $data = []): void
    {
        $relationName = $data['relationName'] ?? null;
        
        $foreignEntity = $data['foreignEntity'] ?? null;

        if ($relationName === 'opportunities' && $foreignEntity) {
            if ($foreignEntity->get('contactId') && $foreignEntity->get('contactId') === $entity->getId()) {
                $foreignEntity->set('contactId', null);

                $this->entityManager->saveEntity($foreignEntity);
            }
        }
    }
}
