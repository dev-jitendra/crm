<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\DateTime;

trait DateTimeSetter
{
    
    protected $dateTime;

    public function setDateTime(DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }
}
