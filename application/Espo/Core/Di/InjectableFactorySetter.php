<?php


namespace Espo\Core\Di;

use Espo\Core\InjectableFactory;

trait InjectableFactorySetter
{
    
    protected $injectableFactory;

    public function setInjectableFactory(InjectableFactory $injectableFactory): void
    {
        $this->injectableFactory = $injectableFactory;
    }
}
