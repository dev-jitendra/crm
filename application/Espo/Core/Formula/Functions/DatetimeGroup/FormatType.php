<?php


namespace Espo\Core\Formula\Functions\DateTimeGroup;

use Espo\Core\Di;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class FormatType extends BaseFunction implements Di\DateTimeAware
{
    use Di\DateTimeSetter;

    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $timezone = null;
        if (count($args) > 1) {
            $timezone = $args[1];
        }
        $value = $args[0];

        $format = null;
        if (count($args) > 2) {
            $format = $args[2];
        }

        if (strlen($value) > 11) {
            return $this->dateTime->convertSystemDateTime($value, $timezone, $format);
        } else {
            return $this->dateTime->convertSystemDate($value, $format);
        }
    }
}
