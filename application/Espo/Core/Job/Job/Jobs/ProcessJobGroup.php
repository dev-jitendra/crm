<?php


namespace Espo\Core\Job\Job\Jobs;

use Espo\Core\Utils\Config;
use Espo\Core\Job\Job;
use Espo\Core\Job\Job\Data;
use Espo\Core\Job\JobManager;

class ProcessJobGroup implements Job
{
    private const PORTION_NUMBER = 100;

    public function __construct(
        private JobManager $jobManager,
        private Config $config
    ) {}

    public function run(Data $data): void
    {
        $limit = $this->config->get('jobGroupMaxPortion') ?? self::PORTION_NUMBER;

        $group = $data->get('group');

        $this->jobManager->processGroup($group, $limit);
    }
}
