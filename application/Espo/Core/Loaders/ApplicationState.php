<?php


namespace Espo\Core\Loaders;

use Espo\Core\ApplicationState as ApplicationStateService;
use Espo\Core\Container;
use Espo\Core\Container\Loader;

class ApplicationState implements Loader
{
    public function __construct(private Container $container)
    {}

    public function load(): ApplicationStateService
    {
        return new ApplicationStateService($this->container);
    }
}
