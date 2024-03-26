<?php


namespace Espo\Modules\Crm\Hooks\Task;

use Espo\Core\Field\DateTime;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Modules\Crm\Entities\Task;
use Espo\ORM\Entity;

class DateCompleted
{
    private const FIELD_DATE_COMPLETED = 'dateCompleted';
    private const FIELD_STATUS = 'status';

    public function __construct() {}

    
    public function beforeSave(Entity $entity, array $options): void
    {
        if (!$entity->isAttributeChanged(self::FIELD_STATUS)) {
            return;
        }

        if (
            ($options[SaveOption::IMPORT] ?? false) &&
            $entity->get(self::FIELD_DATE_COMPLETED)
        ) {
            return;
        }

        if ($entity->getStatus() !== Task::STATUS_COMPLETED) {
            $entity->set(self::FIELD_DATE_COMPLETED, null);

            return;
        }

        $entity->setValueObject(self::FIELD_DATE_COMPLETED, DateTime::createNow());
    }
}
