<?php


namespace Espo\Core\Di;

use Espo\Core\InjectableFactory;

interface InjectableFactoryAware
{
    public function setInjectableFactory(InjectableFactory $injectableFactory): void;
}
