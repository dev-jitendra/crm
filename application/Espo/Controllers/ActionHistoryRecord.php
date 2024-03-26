<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Controllers\RecordBase;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;

use stdClass;

class ActionHistoryRecord extends RecordBase
{
    public function postActionCreate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
    }

    public function putActionUpdate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
    }
}
