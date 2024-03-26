<?php


namespace Espo\Core\Di;

use Espo\Core\Record\HookManager as RecordHookManager;

trait RecordHookManagerSetter
{
    
    protected $recordHookManager;

    public function setRecordHookManager(RecordHookManager $recordHookManager): void
    {
        $this->recordHookManager = $recordHookManager;
    }
}
