<?php


namespace Espo\Core\Job\Job\Jobs;

use Espo\Core\Job\QueueName;

class ProcessJobQueueE0 extends AbstractQueueJob
{
    protected string $queue = QueueName::E0;
}
