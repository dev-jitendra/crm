<?php


namespace Espo\Controllers;

class ScheduledJobLogRecord extends \Espo\Core\Controllers\Record
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }
}
