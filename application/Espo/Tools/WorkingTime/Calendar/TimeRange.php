<?php


namespace Espo\Tools\WorkingTime\Calendar;

class TimeRange
{
    private Time $start;

    private Time $end;

    public function __construct(Time $start, Time $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): Time
    {
        return $this->start;
    }

    public function getEnd(): Time
    {
        return $this->end;
    }
}
