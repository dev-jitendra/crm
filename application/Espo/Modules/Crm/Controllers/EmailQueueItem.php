<?php


namespace Espo\Modules\Crm\Controllers;

class EmailQueueItem extends \Espo\Core\Controllers\Record
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }
}
