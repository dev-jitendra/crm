<?php


namespace Espo\Core\Record;

use Espo\Core\Api\Request;

class ReadParamsFetcher
{
    public function __construct() {}

    public function fetch(Request $request): ReadParams
    {
        return ReadParams::create();
    }
}
