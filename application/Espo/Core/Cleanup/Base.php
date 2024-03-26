<?php


namespace Espo\Core\Cleanup;


abstract class Base extends \Espo\Core\Injectable
{
    protected function init() 
    {
        $this->addDependency('config');
        $this->addDependency('metadata');
        $this->addDependency('entityManager');
        $this->addDependency('fileManager');
    }

    protected function getConfig() 
    {
        return $this->getInjection('config');
    }

    protected function getMetadata() 
    {
        return $this->getInjection('metadata');
    }

    protected function getEntityManager() 
    {
        return $this->getInjection('entityManager');
    }

    protected function getFileManager() 
    {
        return $this->getInjection('fileManager');
    }

    abstract public function process(); 
}
