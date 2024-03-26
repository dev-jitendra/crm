<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\Exceptions\Error;

use Espo\Core\Formula\ArgumentList;

class ValueType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (!count($args)) {
            throw new Error("Bad value.");
        }

        $value = $args[0]->getData();

        if (is_string($value)) {
            $value = str_replace("\\n", "\n", $value);
        }

        return $value;
    }
}
