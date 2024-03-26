<?php


namespace Espo\Entities;

use Espo\Core\ORM\Entity;
use Espo\Tools\WorkingTime\Calendar\WorkingWeekday;
use Espo\Tools\WorkingTime\Calendar\TimeRange;
use Espo\Tools\WorkingTime\Calendar\Time;

use DateTimeZone;

class WorkingTimeCalendar extends Entity
{
    public const ENTITY_TYPE = 'WorkingTimeCalendar';

    public function getTimeZone(): ?DateTimeZone
    {
        $string = $this->get('timeZone');

        if (!$string) {
            return null;
        }

        return new DateTimeZone($string);
    }

    
    public function getTimeRanges(): array
    {
        return self::convertRanges($this->get('timeRanges'));
    }

    
    private function hasCustomWeekdayRanges(int $weekday): bool
    {
        $attribute = 'weekday' . $weekday . 'TimeRanges';

        return $this->get($attribute) !== null && $this->get($attribute) !== [];
    }

    
    private function getWeekdayTimeRanges(int $weekday): array
    {
        $attribute = 'weekday' . $weekday . 'TimeRanges';

        $raw = $this->hasCustomWeekdayRanges($weekday) ?
            $this->get($attribute) :
            $this->get('timeRanges');

        return self::convertRanges($raw);
    }

    
    public function getWorkingWeekdays(): array
    {
        $list = [];

        for ($i = 0; $i <= 6; $i++) {
            if (!$this->get('weekday' . $i)) {
                continue;
            }

            $list[] = new WorkingWeekday($i, $this->getWeekdayTimeRanges($i));
        }

        return $list;
    }

    
    private static function convertRanges(array $ranges): array
    {
        $list = [];

        foreach ($ranges as $range) {
            $list[] = new TimeRange(
                self::convertTime($range[0]),
                self::convertTime($range[1])
            );
        }

        return $list;
    }

    private static function convertTime(string $time): Time
    {
        
        $h = (int) explode(':', $time)[0];
        
        $m = (int) explode(':', $time)[1];

        return new Time($h, $m);
    }
}
