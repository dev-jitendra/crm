<?php


namespace Espo\Modules\Crm\Hooks\Meeting;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Entities\Email;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;

class EmailCreatedEvent
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    
    public function afterRemove(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT])) {
            return;
        }

        $updateQuery = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(Email::ENTITY_TYPE)
            ->set([
                'createdEventId' => null,
                'createdEventType' => null,
            ])
            ->where([
                'createdEventId' => $entity->getId(),
                'createdEventType' => $entity->getEntityType()
            ])
            ->limit(1)
            ->build();

        $this->entityManager->getQueryExecutor()->execute($updateQuery);
    }
}
