<?php


namespace Espo\Core\Record;

use Espo\Core\Api\Request;

class CreateParamsFetcher
{
    public function __construct() {}

    public function fetch(Request $request): CreateParams
    {
        $data = $request->getParsedBody();

        $skipDuplicateCheck = $request->hasHeader('X-Skip-Duplicate-Check') ?
            strtolower($request->getHeader('X-Skip-Duplicate-Check') ?? '') === 'true' :
            $data->_skipDuplicateCheck ?? 
            $data->skipDuplicateCheck ?? 
            $data->forceDuplicate ?? 
            false;

        $duplicateSourceId = $request->getHeader('X-Duplicate-Source-Id');

        return CreateParams::create()
            ->withSkipDuplicateCheck($skipDuplicateCheck)
            ->withDuplicateSourceId($duplicateSourceId);
    }
}
