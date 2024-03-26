<?php


namespace Espo\Core\FieldProcessing\Reminder;

use Espo\Modules\Crm\Entities\Reminder;
use Espo\ORM\Collection;
use Espo\ORM\Entity;

use Espo\Core\FieldProcessing\Loader as LoaderInterface;
use Espo\Core\FieldProcessing\Loader\Params;
use Espo\Core\ORM\EntityManager;


class Loader implements LoaderInterface
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity, Params $params): void
    {
        $hasReminder = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType())
            ->hasField('reminders');

        if (!$hasReminder) {
            return;
        }

        if ($params->hasSelect() && !$params->hasInSelect('reminders')) {
            return;
        }

        $entity->set('reminders', $this->fetchReminderDataList($entity));
    }

    
    private function fetchReminderDataList(Entity $entity): array
    {
        $list = [];

        
        $collection = $this->entityManager
            ->getRDBRepository(Reminder::ENTITY_TYPE)
            ->select(['seconds', 'type'])
            ->where([
                'entityType' => $entity->getEntityType(),
                'entityId' => $entity->getId(),
            ])
            ->distinct()
            ->order('seconds')
            ->find();

        foreach ($collection as $reminder) {
            $list[] = (object) [
                'seconds' => $reminder->getSeconds(),
                'type' => $reminder->getType(),
            ];
        }

        return $list;
    }
}
