<?php


namespace Espo\Core\Mail\Event;

use ICal\Event as ICalEvent;
use ICal\ICal as U01jmg3ICal;

use RuntimeException;

class EventFactory
{
    public static function createFromU01jmg3Ical(U01jmg3ICal $ical): Event
    {
        
        $event = $ical->events()[0] ?? null;

        if (!$event) {
            throw new RuntimeException();
        }

        $dateStart = $event->dtstart_tz ?? null;
        $dateEnd = $event->dtend_tz ?? null;

        $isAllDay = strlen($event->dtstart) === 8;

        if ($isAllDay) {
            $dateStart = $event->dtstart ?? null;
            $dateEnd = $event->dtend ?? null;
        }

        return Event::create()
            ->withUid($event->uid ?? null)
            ->withIsAllDay($isAllDay)
            ->withDateStart($dateStart)
            ->withDateEnd($dateEnd)
            ->withName($event->summary ?? null)
            ->withLocation($event->location ?? null)
            ->withDescription($event->description ?? null)
            ->withTimezone($ical->calendarTimeZone() ?? null) 
            ->withOrganizer($event->organizer ?? null)
            ->withAttendees($event->attendee ?? null);
    }
}
