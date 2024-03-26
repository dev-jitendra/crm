<?php


namespace Espo\Classes\Jobs;

use Espo\Core\Job\JobDataLess;
use Espo\Core\Webhook\Queue;

class ProcessWebhookQueue implements JobDataLess
{
    public function __construct(private Queue $queue)
    {}

    public function run(): void
    {
        $this->queue->process();
    }
}
