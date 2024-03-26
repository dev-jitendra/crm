<?php


namespace Espo\Controllers;

use Espo\Core\Controllers\Record;

class LayoutSet extends Record
{
    protected function checkAccess(): bool
    {
        return $this->getUser()->isAdmin();
    }
}
