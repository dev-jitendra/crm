<?php


namespace Espo\Controllers;

use Espo\Core\Controllers\Record;

class ScheduledJob extends Record
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }
}
