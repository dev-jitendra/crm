<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\DateTime;

interface DateTimeAware
{
    public function setDateTime(DateTime $dateTime): void;
}
