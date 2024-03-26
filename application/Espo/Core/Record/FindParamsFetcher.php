<?php


namespace Espo\Core\Record;

use Espo\Core\Api\Request;

class FindParamsFetcher
{
    public function __construct() {}

    public function fetch(Request $request): FindParams
    {
        $noTotal = strtolower($request->getHeader('X-No-Total') ?? '') === 'true';

        if ($request->getQueryParam('q')) {
            $noTotal = true;
        }

        return FindParams::create()
            ->withNoTotal($noTotal);
    }
}
