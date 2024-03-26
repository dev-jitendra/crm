<?php


namespace Espo\Core\Di;

use Espo\Core\Record\HookManager as RecordHookManager;

interface RecordHookManagerAware
{
    public function setRecordHookManager(RecordHookManager $recordHookManager): void;
}
