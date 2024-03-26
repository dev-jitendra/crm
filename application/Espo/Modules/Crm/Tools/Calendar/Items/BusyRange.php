<?php


namespace Espo\Modules\Crm\Tools\Calendar\Items;

use Espo\Core\Field\DateTime;
use Espo\Modules\Crm\Tools\Calendar\Item;

use stdClass;

class BusyRange implements Item
{
    private DateTime $start;
    private DateTime $end;

    public function __construct(DateTime $start, DateTime $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    public function getRaw(): stdClass
    {
        return (object) [
            'dateStart' => $this->start->toString(),
            'dateEnd' => $this->end->toString(),
            'isBusyRange' => true,
        ];
    }
}
