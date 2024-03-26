<?php


namespace Espo\Hooks\Common;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\ORM\Entity;

use Espo\Tools\Stream\Service as Service;


class StreamNotesAcl
{
    public static int $order = 10;

    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    
    public function afterSave(Entity $entity, array $options): void
    {
        if (!empty($options['noStream'])) {
            return;
        }

        if (!empty($options[SaveOption::SILENT])) {
            return;
        }

        if (!empty($options['skipStreamNotesAcl'])) {
            return;
        }

        if ($entity->isNew()) {
            return;
        }

        $forceProcessNoteNotifications = !empty($options['forceProcessNoteNotifications']);

        $this->service->processNoteAcl($entity, $forceProcessNoteNotifications);
    }
}
