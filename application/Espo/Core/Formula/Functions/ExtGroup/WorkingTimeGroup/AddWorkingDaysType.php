<?php


namespace Espo\Core\Formula\Functions\ExtGroup\WorkingTimeGroup;

use Espo\Core\Field\DateTime;
use Espo\Core\Field\DateTimeOptional;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Utils\DateTime as DateTimeUtil;

class AddWorkingDaysType extends Base
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments(2);
        }

        
        $evaluatedArgs = $this->evaluate($args);

        $stringValue = $evaluatedArgs[0];
        $days = $evaluatedArgs[1];

        if (!is_string($stringValue)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!is_int($days) && !is_float($days)) {
            $this->throwBadArgumentType(2, 'int');
        }

        if (is_float($days)) {
            $days = (int) $days;
        }

        if ($days <= 0) {
            $this->throwBadArgumentValue(2, 'Days value should be greater than 0.');
        }

        $calendar = $this->createCalendar($evaluatedArgs, 2);

        $dateTime = DateTimeOptional::fromString($stringValue);

        $isAllDay = $dateTime->isAllDay();

        if ($isAllDay) {
            $dateTime = $dateTime->withTimezone($calendar->getTimezone());
        }

        $dateTime = DateTime::fromDateTime($dateTime->toDateTime());

        $result = $this->createCalendarUtility($calendar)->addWorkingDays($dateTime, $days);

        if (!$result) {
            return null;
        }

        if ($isAllDay) {
            return $result->toDateTime()->format(DateTimeUtil::SYSTEM_DATE_FORMAT);
        }

        return $result->toString();
    }
}
