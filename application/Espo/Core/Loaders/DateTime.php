<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\DateTime as DateTimeService;

class DateTime implements Loader
{
    public function __construct(private Config $config)
    {}

    public function load(): DateTimeService
    {
        return new DateTimeService(
            $this->config->get('dateFormat'),
            $this->config->get('timeFormat'),
            $this->config->get('timeZone'),
            $this->config->get('language')
        );
    }
}
