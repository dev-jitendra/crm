<?php


namespace Espo\Core\Record;

use Espo\Core\Api\Request;

class DeleteParamsFetcher
{
    public function __construct() {}

    public function fetch(Request $request): DeleteParams
    {
        return DeleteParams::create();
    }
}
