<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Container;
use Espo\Core\DataManager;
use Espo\Core\Api\Request;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\AdminNotificationManager;
use Espo\Core\Utils\SystemRequirements;
use Espo\Core\Utils\ScheduledJob;
use Espo\Core\Upgrades\UpgradeManager;
use Espo\Entities\User;

class Admin
{
    
    public function __construct(
        private Container $container,
        private Config $config,
        private User $user,
        private AdminNotificationManager $adminNotificationManager,
        private SystemRequirements $systemRequirements,
        private ScheduledJob $scheduledJob,
        private DataManager $dataManager
    ) {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }
    }

    
    public function postActionRebuild(): bool
    {
        $this->dataManager->rebuild();

        return true;
    }

    
    public function postActionClearCache(): bool
    {
        $this->dataManager->clearCache();

        return true;
    }

    
    public function getActionJobs(): array
    {
        return $this->scheduledJob->getAvailableList();
    }

    
    public function postActionUploadUpgradePackage(Request $request): object
    {
        if (
            $this->config->get('restrictedMode') &&
            !$this->user->isSuperAdmin()
        ) {
            throw new Forbidden();
        }

        if ($this->config->get('adminUpgradeDisabled')) {
            throw new Forbidden("Disabled with 'adminUpgradeDisabled' parameter.");
        }

        $data = $request->getBodyContents();

        if (!$data) {
            throw new BadRequest();
        }

        $upgradeManager = new UpgradeManager($this->container);

        $upgradeId = $upgradeManager->upload($data);
        $manifest = $upgradeManager->getManifest();

        return (object) [
            'id' => $upgradeId,
            'version' => $manifest['version'],
        ];
    }

    
    public function postActionRunUpgrade(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (
            $this->config->get('restrictedMode') &&
            !$this->user->isSuperAdmin()
        ) {
            throw new Forbidden();
        }

        $upgradeManager = new UpgradeManager($this->container);

        $upgradeManager->install(get_object_vars($data));

        return true;
    }

    
    public function getActionCronMessage(): object
    {
        return (object) $this->scheduledJob->getSetupMessage();
    }

    
    public function getActionAdminNotificationList(): array
    {
        return $this->adminNotificationManager->getNotificationList();
    }

    
    public function getActionSystemRequirementList(): object
    {
        return (object) $this->systemRequirements->getAllRequiredList();
    }
}
