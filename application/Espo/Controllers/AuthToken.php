<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Controllers\Record;
use Espo\Core\Api\Request;

class AuthToken extends Record
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }

    public function postActionCreateLink(Request $request): bool
    {
        throw new Forbidden();
    }

    public function deleteActionRemoveLink(Request $request): bool
    {
        throw new Forbidden();
    }
}
