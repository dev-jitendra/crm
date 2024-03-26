<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container;


abstract class Base implements Loader
{
    protected $container; 

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getContainer() 
    {
        return $this->container;
    }
}
