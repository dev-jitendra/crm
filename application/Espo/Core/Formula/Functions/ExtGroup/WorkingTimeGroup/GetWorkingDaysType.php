<?php


namespace Espo\Core\Formula\Functions\ExtGroup\WorkingTimeGroup;

use Espo\Core\Field\DateTime;
use Espo\Core\Field\DateTimeOptional;
use Espo\Core\Formula\ArgumentList;

class GetWorkingDaysType extends Base
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments(2);
        }

        
        $evaluatedArgs = $this->evaluate($args);

        $stringValue1 = $evaluatedArgs[0];
        $stringValue2 = $evaluatedArgs[1];

        if (!is_string($stringValue1)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!is_string($stringValue2)) {
            $this->throwBadArgumentType(2, 'string');
        }

        $calendar = $this->createCalendar($evaluatedArgs, 2);

        $dateTime1 = DateTimeOptional::fromString($stringValue1);
        $dateTime2 = DateTimeOptional::fromString($stringValue2);

        if ($dateTime1->isAllDay()) {
            $dateTime1 = $dateTime1->withTimezone($calendar->getTimezone());
        }

        if ($dateTime2->isAllDay()) {
            $dateTime2 = $dateTime2->withTimezone($calendar->getTimezone());
        }

        $dateTime1 = DateTime::fromDateTime($dateTime1->getDateTime());
        $dateTime2 = DateTime::fromDateTime($dateTime2->getDateTime());

        return $this->createCalendarUtility($calendar)->getWorkingDays($dateTime1, $dateTime2);
    }
}
