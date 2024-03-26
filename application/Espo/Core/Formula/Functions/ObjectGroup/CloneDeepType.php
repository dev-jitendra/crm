<?php


namespace Espo\Core\Formula\Functions\ObjectGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

use Espo\Core\Utils\ObjectUtil;

use stdClass;

class cloneDeepType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        $obj = $this->evaluate($args[0]);

        if (!$obj instanceof stdClass) {
            $this->throwBadArgumentType(1, 'object');
        }

        return ObjectUtil::clone($obj);
    }
}
