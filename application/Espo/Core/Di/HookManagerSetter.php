<?php


namespace Espo\Core\Di;

use Espo\Core\HookManager;

trait HookManagerSetter
{
    
    protected $hookManager;

    public function setHookManager(HookManager $hookManager): void
    {
        $this->hookManager = $hookManager;
    }
}
