<?php


namespace Espo\Core\Formula\Functions\NumberGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class FormatType extends BaseFunction implements
    Di\NumberAware
{
    use Di\NumberSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $decimals = null;
        if (count($args) > 1) {
            $decimals = $this->evaluate($args[1]);
        }

        $decimalMark = null;
        if (count($args) > 2) {
            $decimalMark = $this->evaluate($args[2]);
        }

        $thousandSeparator = null;
        if (count($args) > 3) {
            $thousandSeparator = $this->evaluate($args[3]);
        }

        $value = $this->evaluate($args[0]);

        return $this->number->format($value, $decimals, $decimalMark, $thousandSeparator);
    }
}
