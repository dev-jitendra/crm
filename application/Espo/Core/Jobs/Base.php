<?php


namespace Espo\Core\Jobs;

use Espo\Core\Container;


abstract class Base
{
    private $container; 

    protected function getContainer() 
    {
        return $this->container;
    }

    protected function getEntityManager() 
    {
        return $this->getContainer()->get('entityManager');
    }

    protected function getServiceFactory() 
    {
        return $this->getContainer()->get('serviceFactory');
    }

    protected function getConfig() 
    {
        return $this->getContainer()->get('config');
    }

    protected function getMetadata() 
    {
        return $this->getContainer()->get('metadata');
    }

    protected function getUser() 
    {
        return $this->getContainer()->get('user');
    }

    public function __construct(Container $container) 
    {
        $this->container = $container;
    }
}
