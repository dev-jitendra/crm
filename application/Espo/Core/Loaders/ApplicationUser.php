<?php


namespace Espo\Core\Loaders;

use Espo\Core\ApplicationUser as Service;
use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;

class ApplicationUser implements Loader
{
    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function load(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
