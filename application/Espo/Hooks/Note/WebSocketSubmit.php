<?php


namespace Espo\Hooks\Note;

use Espo\ORM\Entity;
use Espo\Core\Utils\Config;
use Espo\Core\WebSocket\Submission as WebSocketSubmission;

class WebSocketSubmit
{
    public static int $order = 20;

    public function __construct(
        private WebSocketSubmission $webSocketSubmission,
        private Config $config
    ) {}

    public function afterSave(Entity $entity): void
    {
        if (!$this->config->get('useWebSocket')) {
            return;
        }

        $parentId = $entity->get('parentId');
        $parentType = $entity->get('parentType');

        if (!$parentId) {
            return;
        }

        if (!$parentType) {
            return;
        }

        $data = (object) [
            'createdById' => $entity->get('createdById'),
        ];

        if (!$entity->isNew()) {
            $data->noteId = $entity->getId();
        }

        $topic = "streamUpdate.{$parentType}.{$parentId}";

        $this->webSocketSubmission->submit($topic, null, $data);
    }
}
