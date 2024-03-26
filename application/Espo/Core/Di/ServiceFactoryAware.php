<?php


namespace Espo\Core\Di;

use Espo\Core\ServiceFactory;

interface ServiceFactoryAware
{
    public function setServiceFactory(ServiceFactory $serviceFactory): void;
}
