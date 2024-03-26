<?php


namespace Espo\Modules\Crm\Tools\Calendar;

use Espo\Core\Field\DateTime;

use stdClass;

interface Item
{
    public function getRaw(): stdClass;
}
