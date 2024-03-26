<?php


namespace Espo\Core\Utils;

use Espo\Entities\Extension;
use Espo\ORM\EntityManager;


class AdminNotificationManager
{
    public function __construct(
        private EntityManager $entityManager,
        private Config $config,
        private Language $language,
        private ScheduledJob $scheduledJob
    ) {}

    
    public function getNotificationList(): array
    {
        $notificationList = [];

        if (!$this->config->get('adminNotifications')) {
            return [];
        }

        if ($this->config->get('adminNotificationsCronIsNotConfigured')) {
            if (!$this->isCronConfigured()) {
                $notificationList[] = [
                    'id' => 'cronIsNotConfigured',
                    'type' => 'cronIsNotConfigured',
                    'message' => $this->language->translateLabel('cronIsNotConfigured', 'messages', 'Admin'),
                ];
            }
        }

        if ($this->config->get('adminNotificationsNewVersion')) {
            $instanceNeedingUpgrade = $this->getInstanceNeedingUpgrade();

            if (!empty($instanceNeedingUpgrade)) {
                $message = $this->language->translateLabel('newVersionIsAvailable', 'messages', 'Admin');

                $notificationList[] = [
                    'id' => 'newVersionIsAvailable',
                    'type' => 'newVersionIsAvailable',
                    'message' => $this->prepareMessage($message, $instanceNeedingUpgrade),
                ];
            }
        }

        if ($this->config->get('adminNotificationsNewExtensionVersion')) {
            $extensionsNeedingUpgrade = $this->getExtensionsNeedingUpgrade();

            foreach ($extensionsNeedingUpgrade as $extensionName => $extensionDetails) {
                $label = 'new' . Util::toCamelCase($extensionName, ' ', true) . 'VersionIsAvailable';

                $message = $this->language->get(['Admin', 'messages', $label]);

                if (!$message) {
                    $message = $this->language
                        ->translate('newExtensionVersionIsAvailable', 'messages', 'Admin');
                }

                $notificationList[] = [
                    'id' => 'newExtensionVersionIsAvailable' . Util::toCamelCase($extensionName, ' ', true),
                    'type' => 'newExtensionVersionIsAvailable',
                    'message' => $this->prepareMessage($message, $extensionDetails)
                ];
            }
        }

        if (!$this->config->get('adminNotificationsExtensionLicenseDisabled')) {
            $notificationList = array_merge(
                $notificationList,
                $this->getExtensionLicenseNotificationList()
            );
        }

        return $notificationList;
    }

    private function isCronConfigured(): bool
    {
        return $this->scheduledJob->isCronConfigured();
    }

    
    private function getInstanceNeedingUpgrade(): ?array
    {
        $latestVersion = $this->config->get('latestVersion');

        if (!isset($latestVersion)) {
            return null;
        }

        $currentVersion = $this->config->get('version');

        if ($currentVersion === 'dev') {
            return null;
        }

        if (version_compare($latestVersion, $currentVersion, '>')) {
            return [
                'currentVersion' => $currentVersion,
                'latestVersion' => $latestVersion,
            ];
        }

        return null;
    }

    
    private function getExtensionsNeedingUpgrade(): array
    {
        $extensions = [];

        $latestExtensionVersions = $this->config->get('latestExtensionVersions');

        if (empty($latestExtensionVersions) || !is_array($latestExtensionVersions)) {
            return [];
        }

        foreach ($latestExtensionVersions as $extensionName => $extensionLatestVersion) {
            $currentVersion = $this->getExtensionLatestInstalledVersion($extensionName);

            if (isset($currentVersion) && version_compare($extensionLatestVersion, $currentVersion, '>')) {
                $extensions[$extensionName] = [
                    'currentVersion' => $currentVersion,
                    'latestVersion' => $extensionLatestVersion,
                    'extensionName' => $extensionName,
                ];
            }
        }

        return $extensions;
    }

    private function getExtensionLatestInstalledVersion(string $extensionName): ?string
    {
        $extension = $this->entityManager
            ->getRDBRepository('Extension')
            ->select(['version'])
            ->where([
                'name' => $extensionName,
                'isInstalled' => true,
            ])
            ->order('createdAt', true)
            ->findOne();

        if (!$extension) {
            return null;
        }

        return $extension->get('version');
    }

    
    private function prepareMessage(string $message, array $data = []): string
    {
        foreach ($data as $name => $value) {
            $message = str_replace('{'.$name.'}', $value, $message);
        }

        return $message;
    }

    
    private function getExtensionLicenseNotificationList(): array
    {
        $extensionList = $this->entityManager
            ->getRDBRepositoryByClass(Extension::class)
            ->where([
                'licenseStatus' => [
                    Extension::LICENSE_STATUS_INVALID,
                    Extension::LICENSE_STATUS_EXPIRED,
                    Extension::LICENSE_STATUS_SOFT_EXPIRED,
                ],
            ])
            ->find();

        $list = [];

        foreach ($extensionList as $extension) {
            $message =
                $extension->getLicenseStatusMessage() ??
                $this->getExtensionLicenseMessageLabel($extension);

            if (!$message) {
                continue;
            }

            $message = $this->language->translateLabel($message, 'messages');

            $name = $extension->getName();

            $list[] = [
                'id' => 'newExtensionVersionIsAvailable' . Util::toCamelCase($name, ' ', true),
                'type' => 'newExtensionVersionIsAvailable',
                'message' => $this->prepareMessage($message, ['name' => $name]),
            ];
        }

        return $list;
    }

    private function getExtensionLicenseMessageLabel(Extension $extension): ?string
    {
        $status = $extension->getLicenseStatus();

        if (!$status) {
            return null;
        }

        return 'extensionLicense' . ucfirst(Util::hyphenToCamelCase($status));
    }
}
