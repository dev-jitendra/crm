<?php


namespace Espo\Tools\WorkingTime\Calendar;

use Espo\Core\Field\Date;

class WorkingDate implements HavingRanges
{
    private Date $date;

    
    private array $ranges;

    
    public function __construct(Date $date, array $ranges = [])
    {
        $this->date = $date;
        $this->ranges = $ranges;
    }

    public function getDate(): Date
    {
        return $this->date;
    }

    
    public function getRanges(): array
    {
        return $this->ranges;
    }
}
