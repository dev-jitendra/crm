<?php


namespace Espo\Tools\WorkingTime\Util;

use Espo\Entities\WorkingTimeCalendar;
use Espo\Entities\WorkingTimeRange;
use Espo\Tools\WorkingTime\Calendar\WorkingDate;

class CalendarUtil
{
    private WorkingTimeCalendar $workingTimeCalendar;

    public function __construct(WorkingTimeCalendar $workingTimeCalendar)
    {
        $this->workingTimeCalendar = $workingTimeCalendar;
    }

    
    public function rangeToDates(WorkingTimeRange $range): array
    {
        $isWorking = $range->getType() === WorkingTimeRange::TYPE_WORKING;

        $list = [];

        $pointer = $range->getDateStart();
        $endPlusOne = $range->getDateEnd()->modify('+1 day');

        $defaultTimeRanges = $this->workingTimeCalendar->getTimeRanges();

        while ($pointer->isLessThan($endPlusOne)) {
            $timeRanges = $isWorking ? $range->getTimeRanges() : [];

            if ($isWorking && $timeRanges === null) {
                $timeRanges = $defaultTimeRanges;
            }

            $list[] = new WorkingDate($pointer, $timeRanges ?? []);

            $pointer = $pointer->modify('+1 day');
        }

        return $list;
    }
}
