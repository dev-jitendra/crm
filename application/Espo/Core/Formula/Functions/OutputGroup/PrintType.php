<?php


namespace Espo\Core\Formula\Functions\OutputGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

use stdClass;

class PrintType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) === 0) {
            $this->throwTooFewArguments(1);
        }

        $value = $this->evaluate($args[0]);

        if (is_int($value) || is_float($value)) {
            $value = strval($value);
        }
        else if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }
        else if (is_array($value)) {
            $value = json_encode($value);
        }
        else if ($value instanceof stdClass) {
            $value = json_encode($value);
        }
        else if ($value === null) {
            $value = 'null';
        }

        $variables = $this->getVariables();

        if (!isset($variables->__output)) {
            $variables->__output = '';
        }

        $variables->__output = $variables->__output .= $value;
    }
}
