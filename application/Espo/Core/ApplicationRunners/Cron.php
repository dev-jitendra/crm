<?php


namespace Espo\Core\ApplicationRunners;

use Espo\Core\Application\Runner;
use Espo\Core\Job\JobManager;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Log;


class Cron implements Runner
{
    use Cli;
    use SetupSystemUser;

    public function __construct(private JobManager $jobManager, private Config $config, private Log $log)
    {}

    public function run(): void
    {
        if ($this->config->get('cronDisabled')) {
            $this->log->warning("Cron is not run because it's disabled with 'cronDisabled' param.");

            return;
        }

        $this->jobManager->process();
    }
}
