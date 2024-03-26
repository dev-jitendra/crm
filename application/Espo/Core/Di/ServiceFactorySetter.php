<?php


namespace Espo\Core\Di;

use Espo\Core\ServiceFactory;

trait ServiceFactorySetter
{
    
    protected $serviceFactory;

    public function setServiceFactory(ServiceFactory $serviceFactory): void
    {
        $this->serviceFactory = $serviceFactory;
    }
}
