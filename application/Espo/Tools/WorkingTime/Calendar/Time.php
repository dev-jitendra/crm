<?php


namespace Espo\Tools\WorkingTime\Calendar;

class Time
{
    
    private int $hour;

    
    private int $minute;

    
    public function __construct(int $hour, int $minute)
    {
        $this->hour = $hour;
        $this->minute = $minute;
    }

    
    public function getHour(): int
    {
        return $this->hour;
    }

    
    public function getMinute(): int
    {
        return $this->minute;
    }
}
