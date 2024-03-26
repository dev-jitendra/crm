<?php


namespace Espo\Core\Di;

use Espo\Core\Record\ServiceContainer as RecordServiceContainer;

interface RecordServiceContainerAware
{
    public function setRecordServiceContainer(RecordServiceContainer $recordServiceContainer): void;
}
