<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\DataManager as DataManagerService;
use Espo\Core\InjectableFactory;

class DataManager implements Loader
{

    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function load(): DataManagerService
    {
        return $this->injectableFactory->create(DataManagerService::class);
    }
}
