<?php


namespace Espo\Core\Formula\Functions\ObjectGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

use stdClass;

class ClearType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments(2);
        }

        $obj = $this->evaluate($args[0]);
        $key = $this->evaluate($args[1]);

        if (!$obj instanceof stdClass) {
            $this->throwBadArgumentType(1, 'object');
        }

        if ($key === null) {
            $this->throwBadArgumentType(2);
        }

        unset($obj->$key);

        return $obj;
    }
}
