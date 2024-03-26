<?php


namespace Espo\Hooks\Common;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\ORM\Entity;
use Espo\Tools\EmailNotification\HookProcessor;

class AssignmentEmailNotification
{
    private HookProcessor $processor;

    public function __construct(HookProcessor $processor)
    {
        $this->processor = $processor;
    }

    
    public function afterSave(Entity $entity, array $options): void
    {
        if (
            !empty($options[SaveOption::SILENT]) ||
            !empty($options['noNotifications'])
        ) {
            return;
        }

        $this->processor->afterSave($entity);
    }
}
