<?php


namespace Espo\Core\Formula\Functions\RecordServiceGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

class SkipDuplicateCheckType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (empty($this->getVariables()->__isRecordService)) {
            $this->throwError("Can be called only from API script.");
        }

        $skipDuplicateCheck = $this->getVariables()->__skipDuplicateCheck ?? false;

        return (bool) $skipDuplicateCheck;
    }
}
