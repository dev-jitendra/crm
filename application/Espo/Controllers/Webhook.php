<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\RecordBase;

use stdClass;

class Webhook extends RecordBase
{
    protected function checkAccess(): bool
    {
        if (!$this->user->isAdmin() && !$this->user->isApi()) {
            return false;
        }

        return true;
    }

    public function postActionCreate(Request $request, Response $response): stdClass
    {
        $result = parent::postActionCreate($request, $response);

        $response->setStatus(201);

        return $result;
    }
}
