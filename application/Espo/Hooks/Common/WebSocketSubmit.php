<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\WebSocket\Submission as WebSocketSubmission;

class WebSocketSubmit
{
    public static int $order = 20;

    public function __construct(
        private Metadata $metadata,
        private WebSocketSubmission $webSocketSubmission,
        private Config $config
    ) {}

    
    public function afterSave(Entity $entity, array $options): void
    {
        if ($options[SaveOption::SILENT] ?? false) {
            return;
        }

        if ($entity->isNew()) {
            return;
        }

        if (!$this->config->get('useWebSocket')) {
            return;
        }

        $scope = $entity->getEntityType();
        $id = $entity->getId();

        if (!$this->metadata->get(['scopes', $scope, 'object'])) {
            return;
        }

        $topic = "recordUpdate.{$scope}.{$id}";

        $this->webSocketSubmission->submit($topic, null);
    }
}
