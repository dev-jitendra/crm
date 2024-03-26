<?php


namespace Espo\Classes\Jobs;

use Espo\Core\Job\JobDataLess;

use Espo\Tools\EmailNotification\Processor;

class SendEmailNotifications implements JobDataLess
{
    private $processor;

    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
    }

    public function run(): void
    {
        $this->processor->process();
    }
}
