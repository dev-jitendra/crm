<?php


namespace Espo\Hooks\Note;

use Espo\ORM\Entity;
use Espo\Tools\Notification\NoteHookProcessor;
use Espo\Entities\Note;

class Notifications
{
    public static int $order = 14;

    private $processor;

    public function __construct(NoteHookProcessor $processor)
    {
        $this->processor = $processor;
    }

    
    public function afterSave(Entity $entity, array $options): void
    {
        if (!$entity->isNew() && empty($options['forceProcessNotifications'])) {
            return;
        }

        assert($entity instanceof Note);

        $this->processor->afterSave($entity);
    }
}
