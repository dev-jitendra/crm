<?php


namespace Espo\Tools\AdminNotifications\Jobs;

use Espo\Core\Job\JobDataLess;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;
use Espo\Tools\AdminNotifications\LatestReleaseDataRequester;


class CheckNewVersion implements JobDataLess
{
    private Config $config;
    private ConfigWriter $configWriter;
    private LatestReleaseDataRequester $requester;

    public function __construct(
        Config $config,
        ConfigWriter $configWriter,
        LatestReleaseDataRequester $requester
    ) {
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->requester = $requester;
    }

    public function run(): void
    {
        $config = $this->config;

        if (
            !$config->get('adminNotifications') ||
            !$config->get('adminNotificationsNewVersion')
        ) {
            return;
        }

        $latestRelease = $this->requester->request();

        if ($latestRelease === null) {
            return;
        }

        if (empty($latestRelease['version'])) {
            
            $this->configWriter->set('latestVersion', $latestRelease['version']);

            $this->configWriter->save();

            return;
        }

        if ($config->get('latestVersion') != $latestRelease['version']) {
            $this->configWriter->set('latestVersion', $latestRelease['version']);

            if (!empty($latestRelease['notes'])) {
                
            }

            $this->configWriter->save();

            return;
        }

        if (!empty($latestRelease['notes'])) {
            
        }
    }
}
