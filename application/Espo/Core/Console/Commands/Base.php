<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Container;


abstract class Base
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getContainer(): Container
    {
        return $this->container;
    }
}
