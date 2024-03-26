<?php


namespace Espo\Hooks\Notification;

use Espo\ORM\Entity;

use Espo\Core\Utils\Config;
use Espo\Core\WebSocket\Submission as WebSocketSubmission;

class WebSocketSubmit
{
    public static int $order = 20;

    public function __construct(private WebSocketSubmission $webSocketSubmission, private Config $config)
    {}

    public function afterSave(Entity $entity): void
    {
        if (!$this->config->get('useWebSocket')) {
            return;
        }

        if (!$entity->isNew()) {
            return;
        }

        $userId = $entity->get('userId');

        if (!$userId) {
            return;
        }

        $this->webSocketSubmission->submit('newNotification', $userId);
    }
}
