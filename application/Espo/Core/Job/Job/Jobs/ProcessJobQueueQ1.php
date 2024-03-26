<?php


namespace Espo\Core\Job\Job\Jobs;

use Espo\Core\Job\QueueName;

class ProcessJobQueueQ1 extends AbstractQueueJob
{
    protected string $queue = QueueName::Q1;
}
