<?php


namespace Espo\Tools\WorkingTime\Calendar;

class WorkingWeekday implements HavingRanges
{
    
    private int $weekday;

    
    private array $ranges;

    
    public function __construct(int $weekday, array $ranges)
    {
        $this->weekday = $weekday;
        $this->ranges = $ranges;
    }

    
    public function getWeekday(): int
    {
        return $this->weekday;
    }

    
    public function getRanges(): array
    {
        return $this->ranges;
    }
}
