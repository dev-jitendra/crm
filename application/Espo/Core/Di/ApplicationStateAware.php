<?php


namespace Espo\Core\Di;

use Espo\Core\ApplicationState;

interface ApplicationStateAware
{
    public function setApplicationState(ApplicationState $applicationState): void;
}
