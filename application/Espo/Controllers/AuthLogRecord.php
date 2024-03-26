<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Controllers\Record;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;

use stdClass;

class AuthLogRecord extends Record
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }

    public function postActionCreate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
    }

    public function putActionUpdate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
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
