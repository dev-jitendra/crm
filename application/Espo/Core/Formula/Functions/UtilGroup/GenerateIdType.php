<?php


namespace Espo\Core\Formula\Functions\UtilGroup;

use Espo\Core\Formula\EvaluatedArgumentList;
use Espo\Core\Formula\Func;
use Espo\Core\Utils\Util;

class GenerateIdType implements Func
{
    public function process(EvaluatedArgumentList $arguments): string
    {
        return Util::generateId();
    }
}
