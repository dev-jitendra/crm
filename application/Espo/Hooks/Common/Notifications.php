<?php


namespace Espo\Hooks\Common;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Tools\Notification\HookProcessor;
use Espo\ORM\Entity;

class Notifications
{
    public static int $order = 10;

    private HookProcessor $processor;

    public function __construct(HookProcessor $processor)
    {
        $this->processor = $processor;
    }

    
    public function afterSave(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT]) || !empty($options['noNotifications'])) {
            return;
        }

        $this->processor->afterSave($entity, $options);
    }

    
    public function beforeRemove(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT]) || !empty($options['noNotifications'])) {
            return;
        }

        $this->processor->beforeRemove($entity, $options);
    }

    
    public function afterRemove(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT])) {
            return;
        }

        $this->processor->afterRemove($entity);
    }
}
