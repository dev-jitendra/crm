<?php


namespace Espo\Tools\WorkingTime;

use Espo\Tools\WorkingTime\Calendar\WorkingWeekday;
use Espo\Tools\WorkingTime\Calendar\WorkingDate;

use Espo\Core\Field\Date;

use DateTimeZone;

interface Calendar
{
    
    public function getTimezone(): DateTimeZone;

    
    public function getWorkingWeekdays(): array;

    
    public function getNonWorkingDates(Date $from, Date $to): array;

    
    public function getWorkingDates(Date $from, Date $to): array;
}
