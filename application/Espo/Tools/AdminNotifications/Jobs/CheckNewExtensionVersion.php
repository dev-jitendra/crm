<?php


namespace Espo\Tools\AdminNotifications\Jobs;

use Espo\Core\Job\JobDataLess;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Entities\Extension;
use Espo\ORM\EntityManager;
use Espo\Tools\AdminNotifications\LatestReleaseDataRequester;


class CheckNewExtensionVersion implements JobDataLess
{
    private Config $config;
    private ConfigWriter $configWriter;
    private EntityManager $entityManager;
    private LatestReleaseDataRequester $requester;

    public function __construct(
        Config $config,
        ConfigWriter $configWriter,
        EntityManager $entityManager,
        LatestReleaseDataRequester $requester
    ) {
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->entityManager = $entityManager;
        $this->requester = $requester;
    }

    public function run(): void
    {
        $config = $this->config;

        if (
            !$config->get('adminNotifications') ||
            !$config->get('adminNotificationsNewExtensionVersion')
        ) {
            return;
        }

        $query = $this->entityManager
            ->getQueryBuilder()
            ->select()
            ->from(Extension::ENTITY_TYPE)
            ->select(['id', 'name', 'version', 'checkVersionUrl'])
            ->where([
                'deleted' => false,
                'isInstalled' => true,
            ])
            ->order(['createdAt'])
            ->build();

        $sth = $this->entityManager->getQueryExecutor()->execute($query);

        $latestReleases = [];

        while ($row = $sth->fetch()) {
            $url = !empty($row['checkVersionUrl']) ? $row['checkVersionUrl'] : null;

            $extensionName = $row['name'];

            $latestRelease = $this->requester->request($url, [
                'name' => $extensionName,
            ]);

            if (!empty($latestRelease) && !isset($latestRelease['error'])) {
                $latestReleases[$extensionName] = $latestRelease;
            }
        }

        $latestExtensionVersions = $config->get('latestExtensionVersions', []);

        $save = false;

        foreach ($latestReleases as $extensionName => $extensionData) {
            if (empty($latestExtensionVersions[$extensionName])) {
                $latestExtensionVersions[$extensionName] = $extensionData['version'];
                $save = true;

                continue;
            }

            if ($latestExtensionVersions[$extensionName] != $extensionData['version']) {
                $latestExtensionVersions[$extensionName] = $extensionData['version'];

                if (!empty($extensionData['notes'])) {
                    
                }

                $save = true;

                continue;
            }

            if (!empty($extensionData['notes'])) {
                
            }
        }

        if ($save) {
            $this->configWriter->set('latestExtensionVersions', $latestExtensionVersions);

            $this->configWriter->save();
        }
    }
}
