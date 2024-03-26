<?php


namespace Espo\Core\Job;

use Espo\Core\Utils\Config;

use Spatie\Async\Pool;

class AsyncPoolFactory
{
    public function __construct(private Config $config)
    {}

    public function isSupported(): bool
    {
        return Pool::isSupported();
    }

    public function create(): Pool
    {
        return Pool
            ::create()
            ->autoload(getcwd() . '/vendor/autoload.php')
            ->concurrency($this->config->get('jobPoolConcurrencyNumber'))
            ->timeout($this->config->get('jobPeriodForActiveProcess'));
    }
}
