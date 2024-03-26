<?php


namespace Espo\Core\Formula\Functions\DatetimeGroup;

use Espo\Core\Di;
use Espo\Core\Utils\DateTime as DateTimeUtil;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Functions\BaseFunction;

use DateTime;
use Exception;

abstract class AddIntervalType extends BaseFunction implements Di\DateTimeAware
{
    use Di\DateTimeSetter;

    
    protected $timeOnly = false;

    
    protected $intervalTypeString;

    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $dateTimeString = $args[0];

        if (!$dateTimeString) {
            return null;
        }

        if (!is_string($dateTimeString)) {
            $this->throwBadArgumentType(1, 'string');
        }

        $interval = $args[1];

        if (!is_numeric($interval)) {
            $this->throwBadArgumentType(2, 'numeric');
        }

        $isTime = false;
        if (strlen($dateTimeString) > 10) {
            $isTime = true;
        }

        if ($this->timeOnly && !$isTime) {
            $dateTimeString .= ' 00:00:00';
            $isTime = true;
        }

        try {
            $dateTime = new DateTime($dateTimeString);
        }
        catch (Exception $e) {
            $this->log('bad date-time value passed', 'warning');

            return null;
        }

        $dateTime->modify(
            ($interval > 0 ? '+' : '') . strval($interval) . ' ' . $this->intervalTypeString
        );

        if ($isTime) {
            return $dateTime->format(DateTimeUtil::SYSTEM_DATE_TIME_FORMAT);
        }
        else {
            return $dateTime->format(DateTimeUtil::SYSTEM_DATE_FORMAT);
        }
    }
}
