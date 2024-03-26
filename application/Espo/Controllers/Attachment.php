<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\RecordBase;
use Espo\Core\Exceptions\Forbidden;

use stdClass;

class Attachment extends RecordBase
{
    public function getActionList(Request $request, Response $response): stdClass
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        return parent::getActionList($request, $response);
    }
}
