<?php


namespace Espo\Core\Di;

use Espo\Core\Record\ServiceContainer as RecordServiceContainer;

trait RecordServiceContainerSetter
{
    
    protected $recordServiceContainer;

    public function setRecordServiceContainer(RecordServiceContainer $recordServiceContainer): void
    {
        $this->recordServiceContainer = $recordServiceContainer;
    }
}
