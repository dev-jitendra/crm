<?php


namespace Espo\Core\Di;

use Espo\Core\ApplicationState;

trait ApplicationStateSetter
{
    
    protected $applicationState;

    public function setApplicationState(ApplicationState $applicationState): void
    {
        $this->applicationState = $applicationState;
    }
}
