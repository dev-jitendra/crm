<?php


namespace Espo\Core\Di;

use Espo\Core\Webhook\Manager;

trait WebhookManagerSetter
{
    
    protected $webhookManager;

    public function setWebhookManager(Manager $webhookManager): void
    {
        $this->webhookManager = $webhookManager;
    }
}
