<?php


namespace Espo\Core\Formula\Functions\ObjectGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;


use stdClass;

class SetType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 3) {
            $this->throwTooFewArguments(3);
        }

        $obj = $this->evaluate($args[0]);
        $key = $this->evaluate($args[1]);
        $value = $this->evaluate($args[2]);

        if (!$obj instanceof stdClass) {
            $this->throwBadArgumentType(1, 'object');
        }

        if ($key === null) {
            $this->throwBadArgumentType(2);
        }

        $obj->$key = $value;

        return $obj;
    }
}
