<?php


namespace Espo\Core\Di;

use Espo\Core\HookManager;

interface HookManagerAware
{
    public function setHookManager(HookManager $hookManager): void;
}
