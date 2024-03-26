<?php


namespace Espo\Hooks\Common;

use Espo\ORM\Entity;
use Espo\Core\ORM\Entity as CoreEntity;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Utils\Metadata;
use Espo\Core\Webhook\Manager as WebhookManager;

class Webhook
{
    public static int $order = 101;

    public function __construct(private Metadata $metadata, private WebhookManager $webhookManager)
    {}

    
    public function afterSave(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT])) {
            return;
        }

        if (!$this->metadata->get(['scopes', $entity->getEntityType(), 'object'])) {
            return;
        }

        if (!$entity instanceof CoreEntity) {
            return;
        }

        if ($entity->isNew()) {
            $this->webhookManager->processCreate($entity);
        }
        else {
            $this->webhookManager->processUpdate($entity);
        }
    }

    
    public function afterRemove(Entity $entity, array $options): void
    {
        if (!empty($options[SaveOption::SILENT])) {
            return;
        }

        if (!$this->metadata->get(['scopes', $entity->getEntityType(), 'object'])) {
            return;
        }

        if (!$entity instanceof CoreEntity) {
            return;
        }

        $this->webhookManager->processDelete($entity);
    }
}
