<?php


namespace Espo\Core\Job;

use Espo\Core\InjectableFactory;


class JobSchedulerFactory
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function create(): JobScheduler
    {
        return $this->injectableFactory->create(JobScheduler::class);
    }
}
