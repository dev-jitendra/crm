<?php


namespace Espo\Controllers;

use Espo\Core\Controllers\RecordBase;

class AuthenticationProvider extends RecordBase
{
    protected function checkAccess(): bool
    {
        if (!$this->user->isAdmin()) {
            return false;
        }

        return true;
    }
}
