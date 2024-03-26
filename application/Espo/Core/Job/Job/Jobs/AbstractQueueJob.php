<?php


namespace Espo\Core\Job\Job\Jobs;

use Espo\Core\Job\JobDataLess;
use Espo\Core\Job\JobManager;
use Espo\Core\Job\QueuePortionNumberProvider;

abstract class AbstractQueueJob implements JobDataLess
{
    protected string $queue;

    public function __construct(
        private JobManager $jobManager,
        private QueuePortionNumberProvider $portionNumberProvider)
    {}

    public function run(): void
    {
        $limit = $this->portionNumberProvider->get($this->queue);

        $this->jobManager->processQueue($this->queue, $limit);
    }
}
