<?php


namespace Espo\Core\Formula\Functions\ExtGroup\WorkingTimeGroup;

use Espo\Core\Field\DateTime;
use Espo\Core\Field\DateTimeOptional;
use Espo\Core\Formula\ArgumentList;

class IsWorkingDayType extends Base
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments(1);
        }

        
        $evaluatedArgs = $this->evaluate($args);

        $stringValue = $evaluatedArgs[0];

        if (!is_string($stringValue)) {
            $this->throwBadArgumentType(1, 'string');
        }

        $calendar = $this->createCalendar($evaluatedArgs);

        $dateTime = DateTimeOptional::fromString($stringValue);

        if ($dateTime->isAllDay()) {
            $dateTime = $dateTime->withTimezone($calendar->getTimezone());
        }

        $dateTime = DateTime::fromDateTime($dateTime->getDateTime());

        return $this->createCalendarUtility($calendar)->isWorkingDay($dateTime);
    }
}
