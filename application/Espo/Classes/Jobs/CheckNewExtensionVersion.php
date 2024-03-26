<?php


namespace Espo\Classes\Jobs;

use Espo\Entities\Job;

class CheckNewExtensionVersion extends CheckNewVersion
{
    public function run(): void
    {
        if (
            !$this->config->get('adminNotifications') ||
            !$this->config->get('adminNotificationsNewExtensionVersion')
        ) {
            return;
        }

        $className = \Espo\Tools\AdminNotifications\Jobs\CheckNewExtensionVersion::class;

        $this->entityManager->createEntity(Job::ENTITY_TYPE, [
            'name' => $className,
            'className' => $className,
            'executeTime' => $this->getRunTime(),
        ]);
    }
}
