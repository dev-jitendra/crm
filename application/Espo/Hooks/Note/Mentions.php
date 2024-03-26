<?php


namespace Espo\Hooks\Note;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Tools\Notification\NoteMentionHookProcessor;
use Espo\ORM\Entity;
use Espo\Entities\Note;

class Mentions
{
    public static int $order = 9;

    private NoteMentionHookProcessor $processor;

    public function __construct(NoteMentionHookProcessor $processor)
    {
        $this->processor = $processor;
    }

    
    public function beforeSave(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT])) {
            return;
        }

        assert($entity instanceof Note);

        $this->processor->beforeSave($entity);
    }
}
