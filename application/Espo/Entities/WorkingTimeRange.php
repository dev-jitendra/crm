<?php


namespace Espo\Entities;

use Espo\Core\Field\Date;
use Espo\Core\Field\LinkMultiple;
use Espo\Core\ORM\Entity;
use Espo\Tools\WorkingTime\Calendar\Time;
use Espo\Tools\WorkingTime\Calendar\TimeRange;
use RuntimeException;

class WorkingTimeRange extends Entity
{
    public const ENTITY_TYPE = 'WorkingTimeRange';

    public const TYPE_NON_WORKING = 'Non-working';
    public const TYPE_WORKING = 'Working';

    
    public function getType(): string
    {
        $type = $this->get('type');

        if (!$type) {
            throw new RuntimeException();
        }

        return $type;
    }

    public function getDateStart(): Date
    {
        
        $value = $this->getValueObject('dateStart');

        if (!$value) {
            throw new RuntimeException();
        }

        return $value;
    }

    public function getDateEnd(): Date
    {
        
        $value = $this->getValueObject('dateEnd');

        if (!$value) {
            throw new RuntimeException();
        }

        return $value;
    }

    
    public function getTimeRanges(): ?array
    {
        $ranges = self::convertRanges($this->get('timeRanges') ?? []);

        if ($ranges === []) {
            return null;
        }

        return $ranges;
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

    public function getUsers(): LinkMultiple
    {
        
        return $this->getValueObject('users');
    }
}
