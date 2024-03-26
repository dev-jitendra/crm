<?php


namespace Espo\Core\Record;

use Espo\Core\Api\Request;

class UpdateParamsFetcher
{
    public function __construct() {}

    public function fetch(Request $request): UpdateParams
    {
        $data = $request->getParsedBody();

        $skipDuplicateCheck = $request->hasHeader('X-Skip-Duplicate-Check') ?
            strtolower($request->getHeader('X-Skip-Duplicate-Check') ?? '') === 'true' :
            $data->_skipDuplicateCheck ?? 
            $data->skipDuplicateCheck ?? 
            $data->forceDuplicate ?? 
            false;

        $versionNumber = $request->getHeader('X-Version-Number');

        if ($versionNumber !== null) {
            $versionNumber = intval($versionNumber);
        }

        return UpdateParams::create()
            ->withSkipDuplicateCheck($skipDuplicateCheck)
            ->withVersionNumber($versionNumber);
    }
}
