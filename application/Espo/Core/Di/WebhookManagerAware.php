<?php


namespace Espo\Core\Di;

use Espo\Core\Webhook\Manager;

interface WebhookManagerAware
{
    public function setWebhookManager(Manager $webhookManager): void;
}
