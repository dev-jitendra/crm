<?php


namespace Espo\Core\Job;

use Espo\Core\Utils\Config;

class ConfigDataProvider
{
    public function __construct(private Config $config) {}

    public function runInParallel(): bool
    {
        return (bool) $this->config->get('jobRunInParallel');
    }

    public function getMaxPortion(): int
    {
        return (int) $this->config->get('jobMaxPortion', 0);
    }

    public function getCronMinInterval(): int
    {
        return (int) $this->config->get('cronMinInterval', 0);
    }

    public function noTableLocking(): bool
    {
        return (bool) $this->config->get('jobNoTableLocking');
    }
}
