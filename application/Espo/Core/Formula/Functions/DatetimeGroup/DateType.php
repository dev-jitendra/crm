<?php


namespace Espo\Core\Formula\Functions\DateTimeGroup;

use Espo\Core\Di;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class DateType extends BaseFunction implements Di\DateTimeAware
{
    use Di\DateTimeSetter;

    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $value = $args[0];

        $timezone = null;
        if (count($args) > 1) {
             $timezone = $args[1];
        }

        if (empty($value)) {
            return 0;
        }

        if (strlen($value) > 11) {
            $resultString = $this->dateTime->convertSystemDateTime($value, $timezone, 'D');
        } else {
            $resultString = $this->dateTime->convertSystemDate($value, 'D');
        }

        return intval($resultString);
    }
}
